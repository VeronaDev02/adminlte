// Variáveis globais para controle de estado
let rtspWebsockets = {};
let pdvConnection = null;
let peerConnections = {};
let isConnectedToServer = false;
let pdvMapping = {};
let inactivityAlerts = { queue: [], active: {} };
let isFullScreenMode = false;
let originalSidebarCollapsed = false; // Variável para armazenar o estado original do sidebar

// Configurações de ICE para WebRTC
const iceServers = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};

// Inicializa quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM carregado, verificando configuração:", window.monitorConfig);
    // Verifica se a configuração está disponível
    if (window.monitorConfig) {
        initializeMonitor();
    } else {
        console.error("Configuração não disponível");
    }
    
    // Inicializa os eventos de interface
    initializeInterfaceEvents();
});

// Inicializa o monitor
function initializeMonitor() {
    console.log("Inicializando monitor com config:", window.monitorConfig);
    const { quadrants, columns, rows, connectionConfig, pdvData } = window.monitorConfig;
    
    if (!connectionConfig || !connectionConfig.serverUrls) {
        console.error('Configuração de conexão inválida');
        return;
    }
    
    // Adiciona status do servidor na interface
    const serverStatus = document.createElement('div');
    serverStatus.id = 'serverStatus';
    serverStatus.textContent = 'Conectando ao servidor...';
    document.body.appendChild(serverStatus);
    
    // Conecta ao servidor central
    console.log(`Conectando ao servidor PDV: ${connectionConfig.serverUrls.pdv}`);
    connectToServer(connectionConfig.serverUrls.pdv);
    
    // Para cada PDV, cria mapeamento e inicializa conexões
    if (connectionConfig.connections) {
        Object.entries(connectionConfig.connections).forEach(([position, pdv]) => {
            console.log(`Configurando PDV na posição ${position}:`, pdv);
            
            // Mapeia IP do PDV para posição no grid
            pdvMapping[pdv.pdvIp] = position;
            
            // Inicializa conexão com a câmera
            if (pdv.rtspUrl) {
                console.log(`Iniciando conexão com a câmera ${position} URL: ${pdv.rtspUrl}`);
                connectCamera(position, pdv.rtspUrl, connectionConfig.serverUrls.rtsp);
            } else {
                console.warn(`Sem URL RTSP para a posição ${position}`);
                updateStatus(position, 'Sem URL RTSP');
            }
        });
    }
}

// Conecta ao servidor principal
function connectToServer(serverUrl) {
    const serverStatus = document.getElementById('serverStatus');
    if (!serverStatus) return;
    
    try {
        console.log(`Iniciando conexão WebSocket com: ws://${serverUrl}`);
        pdvConnection = new WebSocket(`ws://${serverUrl}`);
        
        pdvConnection.onopen = () => {
            console.log('Conectado ao servidor PDV');
            serverStatus.textContent = 'Conectado ao servidor';
            serverStatus.classList.add('connected');
            isConnectedToServer = true;
            
            // Registra todos os PDVs agora que estamos conectados
            if (window.monitorConfig && window.monitorConfig.connectionConfig) {
                Object.entries(window.monitorConfig.connectionConfig.connections).forEach(([position, pdv]) => {
                    if (pdv.pdvIp) {
                        console.log(`Registrando PDV ${pdv.pdvIp} na posição ${position}`);
                        registerPDV(position, pdv.pdvIp);
                    }
                });
            }
        };
        
        pdvConnection.onclose = () => {
            console.log('Desconectado do servidor PDV');
            serverStatus.textContent = 'Desconectado do servidor';
            serverStatus.classList.remove('connected');
            isConnectedToServer = false;
        };
        
        pdvConnection.onerror = (error) => {
            console.error('Erro na conexão com o servidor PDV:', error);
            serverStatus.textContent = 'Erro na conexão com o servidor';
            serverStatus.classList.remove('connected');
            isConnectedToServer = false;
        };
        
        // Configura handler para mensagens
        setupMessageHandler();
    } catch (error) {
        console.error('Falha ao conectar ao servidor:', error);
        serverStatus.textContent = 'Falha na conexão com o servidor';
        serverStatus.classList.remove('connected');
    }
}

// Conecta a câmera RTSP
function connectCamera(position, rtspUrl, serverUrl) {
    console.log(`DEBUG: Tentando conectar câmera na posição ${position}`);
    console.log(`DEBUG: URL RTSP completa: ${rtspUrl}`);
    const videoElement = document.getElementById(`remoteVideo${position}`);
    const statusElement = document.getElementById(`status${position}`);
    
    if (!videoElement || !statusElement) {
        console.error(`Elementos de vídeo ou status não encontrados para a posição ${position}`);
        return;
    }
    
    // Fecha conexão existente, se houver
    if (rtspWebsockets[position]) {
        rtspWebsockets[position].close();
    }
    if (peerConnections[position]) {
        peerConnections[position].close();
    }
    
    // Atualiza interface
    statusElement.textContent = 'Conectando câmera...';
    
    try {
        console.log(`Iniciando WebSocket para RTSP na posição ${position}: ws://${serverUrl}`);
        // Cria nova conexão WebSocket para RTSP
        rtspWebsockets[position] = new WebSocket(`ws://${serverUrl}`);
        
        rtspWebsockets[position].onopen = async () => {
            console.log(`WebSocket ${position} conectado. Enviando URL RTSP:`, rtspUrl);
            rtspWebsockets[position].send(rtspUrl);
        };
        
        rtspWebsockets[position].onmessage = async (event) => {
            try {
                console.log(`Mensagem recebida da câmera ${position}:`, event.data.substring(0, 100) + '...');
                const message = JSON.parse(event.data);
                
                // Verifica se a mensagem contém sdp (oferta SDP)
                if (message.sdp && message.type === 'offer') {
                    console.log(`Recebida oferta SDP para câmera ${position}`);
                    await handleOffer(position, message, videoElement);
                    statusElement.textContent = `Conectado - Câmera ${position}`;
                } else {
                    console.log(`Câmera ${position} recebeu mensagem:`, message);
                }
            } catch (error) {
                console.error(`Erro ao processar mensagem na câmera ${position}:`, error);
                statusElement.textContent = 'Erro na câmera';
            }
        };
        
        rtspWebsockets[position].onclose = () => {
            console.log(`WebSocket ${position} fechado`);
            statusElement.textContent = 'Câmera desconectada';
        };
        
        rtspWebsockets[position].onerror = (error) => {
            console.error(`Erro no WebSocket ${position}:`, error);
            statusElement.textContent = 'Erro na câmera';
        };
    } catch (error) {
        console.error(`Erro ao conectar à câmera ${position}:`, error);
        statusElement.textContent = 'Erro na câmera';
    }
}

// Registra PDV no servidor
function registerPDV(position, pdvIp) {
    if (!isConnectedToServer || !pdvConnection) {
        console.error('Não conectado ao servidor. Impossível registrar PDV.');
        return;
    }
    
    const logContainer = document.getElementById(`log${position}`);
    const statusElement = document.getElementById(`status${position}`);
    
    if (!logContainer || !statusElement) {
        console.error(`Elementos de log ou status não encontrados para a posição ${position}`);
        return;
    }
    
    // Adiciona log inicial
    logContainer.innerHTML += `[INFO] Conectando ao PDV ${pdvIp}...\n`;
    statusElement.textContent = 'Conectando PDV...';
    
    // Envia comando de registro para o PDV
    try {
        const registerCommand = {
            command: "register",
            pdv_ip: pdvIp
        };
        
        console.log(`Enviando registro para PDV ${pdvIp}:`, registerCommand);
        pdvConnection.send(JSON.stringify(registerCommand));
    } catch (error) {
        console.error(`Erro ao conectar ao PDV ${position}:`, error);
        logContainer.innerHTML += '[ERRO] Falha na conexão com o PDV\n';
        statusElement.textContent = 'Erro PDV';
    }
}

// Handler centralizado para mensagens do servidor PDV
function setupMessageHandler() {
    if (!pdvConnection) {
        console.error("Sem conexão PDV para configurar handler de mensagem");
        return;
    }
    
    pdvConnection.onmessage = (event) => {
        try {
            // console.log("Mensagem recebida do servidor:", event.data.substring(0, 100) + '...');
            const message = JSON.parse(event.data);
            
            // Se for uma resposta de registro, processa
            if (message.type === 'register_response') {
                handleRegisterResponse(message);
            }
            // Se for dados do PDV, exibe no log do quadrante correspondente
            else if (message.type === 'pdv_data') {
                handlePdvData(message);
            }
            // Se for alerta de inatividade do PDV
            else if (message.type === 'pdv_inativo_timeout') {
                handleInactivityAlert(message);
            }
        } catch (error) {
            console.error('Erro ao processar mensagem do PDV:', error);
        }
    };
}

// Processa resposta de registro
function handleRegisterResponse(message) {
    const pdvIp = message.pdv_ip;
    const position = pdvMapping[pdvIp];
    
    if (position) {
        const logContainer = document.getElementById(`log${position}`);
        const statusElement = document.getElementById(`status${position}`);
        
        if (message.success) {
            console.log(`Registrado com sucesso para o PDV ${pdvIp}`);
            statusElement.textContent = `Conectado - PDV ${pdvIp}`;
            logContainer.innerHTML += `[INFO] Registrado no PDV ${pdvIp}\n`;
        } else {
            console.log(`Falha ao registrar para o PDV ${pdvIp}`);
            statusElement.textContent = 'Falha - PDV';
            logContainer.innerHTML += `[ERRO] Falha ao registrar no PDV ${pdvIp}\n`;
        }
    }
}

// Processa dados do PDV
function handlePdvData(message) {
    const pdvIp = message.pdv_ip;
    const position = pdvMapping[pdvIp];
    
    if (position) {
        const logContainer = document.getElementById(`log${position}`);
        
        if (!logContainer) {
            console.error(`Elemento de log não encontrado para a posição ${position}`);
            return;
        }
        
        // Formata a data/hora atual
        const now = new Date();
        const timestamp = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
        
        // Adiciona a mensagem ao log e escapa caracteres HTML
        const messageText = message.data || '';
        const safeMessage = messageText.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        logContainer.innerHTML += `[${timestamp}] ${safeMessage}\n`;
        
        // Mantém o scroll no final do log
        logContainer.scrollTop = logContainer.scrollHeight;
        
        // console.log(`Mensagem do PDV ${pdvIp} exibida no quadrante ${position}`);
    } else {
        console.warn(`Recebida mensagem do PDV ${pdvIp}, mas não há quadrante associado`);
    }
}

// Processa alerta de inatividade
function handleInactivityAlert(message) {
    const pdvIp = message.pdv_ip;
    const position = pdvMapping[pdvIp];
    
    if (position) {
        // Adiciona o alerta à fila
        addInactivityAlert(position, pdvIp, message.inactive_time);
        
        // Adiciona mensagem ao log
        const logContainer = document.getElementById(`log${position}`);
        
        if (!logContainer) {
            console.error(`Elemento de log não encontrado para a posição ${position}`);
            return;
        }
        
        // Formata a data/hora atual
        const now = new Date();
        const timestamp = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
        
        // Adiciona a mensagem ao log
        logContainer.innerHTML += `[${timestamp}] [ALERTA] PDV ${pdvIp} inativo por ${message.inactive_time} segundos!\n`;
        
        // Mantém o scroll no final do log
        logContainer.scrollTop = logContainer.scrollHeight;
        
        console.log(`Alerta de inatividade do PDV ${pdvIp} no quadrante ${position} por ${message.inactive_time} segundos`);
    }
}

// Função para atualizar status de um quadrante
function updateStatus(position, message) {
    const statusElement = document.getElementById(`status${position}`);
    if (statusElement) {
        statusElement.textContent = message;
    }
}

// Função para lidar com a oferta SDP do servidor
async function handleOffer(position, offer, videoElement) {
    try {
        // Fecha conexão existente, se houver
        if (peerConnections[position]) {
            peerConnections[position].close();
        }
        
        console.log(`Criando RTCPeerConnection para câmera ${position}`);
        
        // Cria uma nova conexão RTCPeerConnection
        peerConnections[position] = new RTCPeerConnection(iceServers);
        
        // Configura os handlers de eventos
        peerConnections[position].ontrack = (event) => {
            if (event.track.kind === 'video') {
                console.log(`DEBUG: Recebido stream de vídeo para câmera ${position}`);
                
                // Adicione logs detalhados para o stream recebido
                console.log(`Stream recebido:`, event.streams[0]);
                console.log(`Tracks no stream:`, event.streams[0].getTracks());
                
                videoElement.srcObject = event.streams[0];
                
                // Adicione eventos para monitorar o estado do vídeo
                videoElement.onloadedmetadata = () => {
                    console.log(`DEBUG: Vídeo metadata carregada para câmera ${position}`);
                    // Tente iniciar a reprodução explicitamente
                    videoElement.play().then(() => {
                        console.log(`Vídeo iniciado com sucesso para câmera ${position}`);
                    }).catch(err => {
                        console.error(`Erro ao iniciar vídeo para câmera ${position}:`, err);
                    });
                };
                
                videoElement.onplay = () => {
                    console.log(`DEBUG: Vídeo iniciando reprodução para câmera ${position}`);
                };
                
                videoElement.onerror = (error) => {
                    console.error(`DEBUG: Erro no elemento video da câmera ${position}:`, error);
                };
                
                // Adicione estes logs para garantir que o elemento esteja configurado
                console.log(`Elemento de vídeo configurado:`, {
                    autoplay: videoElement.autoplay,
                    muted: videoElement.muted,
                    playsinline: videoElement.playsinline,
                    width: videoElement.offsetWidth,
                    height: videoElement.offsetHeight,
                    style: window.getComputedStyle(videoElement)
                });
                
                console.log(`Câmera ${position}: Stream de vídeo conectado`);
                updateStatus(position, `Conectado - Câmera ${position}`);
            }
        };
        
        peerConnections[position].onicecandidate = (event) => {
            if (event.candidate === null) {
                // ICE gathering completed, envia a resposta final
                console.log(`ICE gathering completo para câmera ${position}, enviando resposta`);
                sendAnswer(position);
            }
        };
        
        peerConnections[position].oniceconnectionstatechange = () => {
            const state = peerConnections[position].iceConnectionState;
            console.log(`ICE connection state para câmera ${position}:`, state);
            
            // Atualiza status na interface
            const statusElement = document.getElementById(`status${position}`);
            
            if (!statusElement) return;
            
            if (state === 'connected' || state === 'completed') {
                statusElement.textContent = `Conectado - Câmera ${position}`;
            } else if (state === 'failed' || state === 'disconnected' || state === 'closed') {
                statusElement.textContent = `Câmera ${state}`;
            }
        };
        
        // Define a oferta remota
        console.log(`Definindo oferta remota para câmera ${position}`);
        await peerConnections[position].setRemoteDescription({
            type: offer.type,
            sdp: offer.sdp
        });
        
        // Cria a resposta
        console.log(`Criando resposta para câmera ${position}`);
        const answer = await peerConnections[position].createAnswer();
        await peerConnections[position].setLocalDescription(answer);
        
        console.log(`Resposta SDP para câmera ${position} criada com sucesso`);
    } catch (error) {
        console.error(`Erro ao processar oferta para câmera ${position}:`, error);
        updateStatus(position, 'Erro WebRTC');
    }
}

// Função para enviar a resposta SDP para o servidor
function sendAnswer(position) {
    try {
        if (peerConnections[position] && peerConnections[position].localDescription && rtspWebsockets[position]) {
            const answer = {
                type: peerConnections[position].localDescription.type,
                sdp: peerConnections[position].localDescription.sdp
            };
            
            console.log(`Enviando resposta SDP para câmera ${position}`);
            rtspWebsockets[position].send(JSON.stringify(answer));
        } else {
            console.error(`Não foi possível enviar resposta para câmera ${position}: RTCPeerConnection ou WebSocket não disponíveis`);
        }
    } catch (error) {
        console.error(`Erro ao enviar resposta para câmera ${position}:`, error);
    }
}

// Adiciona um novo alerta de inatividade à fila
function addInactivityAlert(position, pdvIp, inactiveTime) {
    // Se já existe um alerta para esta posição, não faça nada
    if (inactivityAlerts.active[position]) {
        console.log(`Alerta já ativo para o quadrante ${position}`);
        return;
    }
    
    // Cria o objeto de alerta
    const alert = {
        position,
        pdvIp,
        inactiveTime
    };
    
    // Adiciona à fila
    inactivityAlerts.queue.push(alert);
    
    // Se não há alertas ativos nesta posição, inicia o alerta
    if (!inactivityAlerts.active[position]) {
        processNextAlert();
    }
}

// Processa o próximo alerta na fila
function processNextAlert() {
    // Se não há alertas na fila, não faz nada
    if (inactivityAlerts.queue.length === 0) {
        return;
    }
    
    // Pega o próximo alerta da fila
    const alert = inactivityAlerts.queue.shift();
    
    // Mostra o alerta
    showInactivityAlert(alert);
}

// Função para exibir alerta de inatividade
function showInactivityAlert(alert) {
    // Marca como ativo
    inactivityAlerts.active[alert.position] = alert;
    
    const quadrantElement = document.getElementById(`quadrant${alert.position}`);
    const logContainer = document.getElementById(`log${alert.position}`);
    
    if (quadrantElement && logContainer) {
        // Adiciona a classe de alerta
        logContainer.classList.add('inactivity-alert');
        
        // Adiciona ou atualiza um elemento de notificação
        let notificationElement = document.createElement('div');
        notificationElement.className = 'pdv-notification';
        notificationElement.textContent = `PDV ${alert.pdvIp} inativo por ${alert.inactiveTime}s`;
        logContainer.appendChild(notificationElement);
        
        console.log(`Iniciado alerta visual para quadrante ${alert.position}`);
    }
}

// =============================================
// === FUNÇÕES DE GERENCIAMENTO DE FULLSCREEN ===
// =============================================

// Inicializa eventos de interface
function initializeInterfaceEvents() {
    // Adiciona event listeners de duplo clique para todos os quadrantes
    const quadrants = document.querySelectorAll('.stream-container');
    quadrants.forEach(quadrant => {
        quadrant.addEventListener('dblclick', function() {
            toggleQuadrantFullscreen(this);
        });
    });
    
    // Adiciona listener para teclas F11 e Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'F11') {
            // Previne o comportamento padrão do F11
            event.preventDefault();
            toggleBrowserFullscreen();
        } else if (event.key === 'Escape') {
            // Sai do fullscreen de quadrante com ESC
            const fullscreenQuadrant = document.querySelector('.stream-container.fullscreen');
            if (fullscreenQuadrant) {
                toggleQuadrantFullscreen(fullscreenQuadrant);
            }
            // Também sai do modo F11 fullscreen com ESC
            else if (isFullScreenMode) {
                exitBrowserFullscreen();
            }
        }
    });
    
    // Adiciona listeners para eventos de fullscreen do navegador
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);
}

// Função para alternar entre modo normal e tela cheia para um quadrante específico
function toggleQuadrantFullscreen(element) {
    const allQuadrants = document.querySelectorAll('.stream-container');
    
    if (element.classList.contains('fullscreen')) {
        // Sai do modo fullscreen do quadrante
        element.classList.remove('fullscreen');
        
        // Mostra todos os quadrantes
        allQuadrants.forEach(quadrant => {
            if (quadrant.hasAttribute('data-original-display')) {
                quadrant.style.display = quadrant.getAttribute('data-original-display');
                quadrant.removeAttribute('data-original-display');
            } else {
                quadrant.style.display = 'flex';
            }
        });
        
        // Restaura o tamanho original e estilos - limpa todos os estilos inline
        element.style = '';
        
        // Restaura os estilos dos containers internos - limpa todos os estilos inline
        const videoContainer = element.querySelector('.video-container');
        const logContainer = element.querySelector('.log-container');
        
        if (videoContainer) {
            videoContainer.style = '';
        }
        
        if (logContainer) {
            logContainer.style = '';
        }
        
        // Esconde a mensagem de instrução
        const instruction = document.getElementById('fullscreen-instruction');
        if (instruction) {
            instruction.style.display = 'none';
        }
        
        // Restaura o estado original do sidebar
        if (originalSidebarCollapsed) {
            document.body.classList.add('sidebar-collapse');
            console.log('Sidebar colapsado para retornar ao estado original');
        }
    } else {
        // Guarda o estado original do sidebar
        originalSidebarCollapsed = document.body.classList.contains('sidebar-collapse');
        
        // Se o sidebar estiver colapsado, expande-o para o modo fullscreen
        if (originalSidebarCollapsed) {
            document.body.classList.remove('sidebar-collapse');
            console.log('Sidebar expandido para entrar em modo fullscreen de quadrante');
        }
        
        // Salva o estado de exibição atual de todos os quadrantes
        allQuadrants.forEach(quadrant => {
            if (quadrant !== element) {
                quadrant.setAttribute('data-original-display', quadrant.style.display || 'flex');
                quadrant.style.display = 'none';
            }
        });
        
        // Entra no modo fullscreen do quadrante
        element.classList.add('fullscreen');
        
        // Força o posicionamento e tamanho corretos
        element.style.position = 'fixed';
        element.style.top = '0';
        element.style.left = '0';
        element.style.right = '0';
        element.style.bottom = '0';
        element.style.width = '100vw';
        element.style.height = '100vh';
        element.style.maxWidth = 'none'; // Em vez de 100vw
        element.style.minWidth = '100vw';
        element.style.maxHeight = 'none'; // Em vez de 100vh
        element.style.minHeight = '100vh';
        element.style.margin = '0';
        element.style.padding = '0';
        element.style.zIndex = '9999';
        element.style.boxSizing = 'border-box';
        element.style.transform = 'none';
        element.style.border = 'none';
        element.style.borderRadius = '0';
        element.style.overflow = 'hidden';
        
        // Ajusta os containers internos para garantir proporções corretas
        const videoContainer = element.querySelector('.video-container');
        const logContainer = element.querySelector('.log-container');
        
        if (videoContainer) {
            videoContainer.style.flex = '0 0 70%';
            videoContainer.style.width = '70%';
            videoContainer.style.maxWidth = '70%';
            videoContainer.style.minWidth = '70%';
            videoContainer.style.height = '100vh';
            videoContainer.style.margin = '0';
            videoContainer.style.padding = '0';
            videoContainer.style.overflow = 'hidden';
        }
        
        if (logContainer) {
            logContainer.style.flex = '0 0 30%';
            logContainer.style.width = '30%';
            logContainer.style.maxWidth = '30%';
            logContainer.style.minWidth = '30%';
            logContainer.style.height = '100vh';
            logContainer.style.margin = '0';
            logContainer.style.overflow = 'auto';
        }
        
        // Mostra a mensagem de instrução
        const instruction = document.getElementById('fullscreen-instruction');
        if (instruction) {
            instruction.style.display = 'block';
        }
        
        // Força redefinição do layout
        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
        }, 50);
    }
}

// Função para alternar modo F11 fullscreen
function toggleBrowserFullscreen() {
    if (!isFullScreenMode) {
        enterBrowserFullscreen();
    } else {
        exitBrowserFullscreen();
    }
}

// Função para entrar no modo fullscreen do navegador
function enterBrowserFullscreen() {
    // Guarda o estado original do sidebar
    originalSidebarCollapsed = document.body.classList.contains('sidebar-collapse');
    
    // Se o sidebar estiver colapsado, expande-o
    if (originalSidebarCollapsed) {
        // Remove a classe para expandir o sidebar
        document.body.classList.remove('sidebar-collapse');
        console.log('Sidebar expandido para entrar em modo fullscreen');
    }
    
    const docElm = document.documentElement;
    
    if (docElm.requestFullscreen) {
        docElm.requestFullscreen();
    } else if (docElm.mozRequestFullScreen) {
        docElm.mozRequestFullScreen();
    } else if (docElm.webkitRequestFullscreen) {
        docElm.webkitRequestFullscreen();
    } else if (docElm.msRequestFullscreen) {
        docElm.msRequestFullscreen();
    }
    
    document.body.classList.add('browser-fullscreen');
    isFullScreenMode = true;
    
    // Atualiza o botão de controle
    const fullscreenBtn = document.getElementById('browserFullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i> Sair da Tela Cheia';
    }
    
    // Ajusta layout para aproveitar a tela inteira
    adjustFullscreenLayout();
    
    // Checa se há algum quadrante em modo fullscreen
    const fullscreenQuadrant = document.querySelector('.stream-container.fullscreen');
    if (fullscreenQuadrant) {
        fullscreenQuadrant.style.width = '100vw';
        fullscreenQuadrant.style.height = '100vh';
    }
}

// Função para sair do modo fullscreen do navegador
function exitBrowserFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
    }
    
    document.body.classList.remove('browser-fullscreen');
    isFullScreenMode = false;
    
    // Restaura o estado original do sidebar
    if (originalSidebarCollapsed) {
        document.body.classList.add('sidebar-collapse');
        console.log('Sidebar colapsado para retornar ao estado original');
    }
    
    // Atualiza o botão de controle
    const fullscreenBtn = document.getElementById('browserFullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.innerHTML = '<i class="fas fa-desktop"></i> Tela Cheia (F11)';
    }
    
    // Restaura o layout original
    restoreNormalLayout();
    
    // Restaura tamanho normal de quadrante em fullscreen
    const fullscreenQuadrant = document.querySelector('.stream-container.fullscreen');
    if (fullscreenQuadrant) {
        fullscreenQuadrant.style.width = '100%';
        fullscreenQuadrant.style.height = '100%';
    }
}