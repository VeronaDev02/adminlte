// monitor.js - JavaScript essencial para WebRTC e conexões WebSocket

// Estado global mínimo necessário
const state = {
    rtspWebsockets: {},
    pdvConnection: null,
    peerConnections: {},
    isConnectedToServer: false,
    pdvMapping: {},
    inactivityAlerts: { queue: [], active: {} },
    isFullScreenMode: false,
    originalSidebarCollapsed: false
};

// Configurações de ICE para WebRTC
const iceServers = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};

// Inicialização principal
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM carregado, inicializando monitor");
    
    // Verifica se a configuração está disponível
    if (window.monitorConfig) {
        initializeMonitor();
        initializeInterfaceEvents();
    } else {
        console.error("Configuração não disponível");
    }
});

// Inicializa o monitor
function initializeMonitor() {
    const { connectionConfig } = window.monitorConfig;
    
    if (!connectionConfig || !connectionConfig.serverUrls) {
        console.error('Configuração de conexão inválida');
        return;
    }
    
    // Conecta ao servidor central
    console.log(`Conectando ao servidor PDV: ${connectionConfig.serverUrls.pdv}`);
    connectToServer(connectionConfig.serverUrls.pdv);
    
    // Para cada PDV, cria mapeamento e inicializa conexões
    if (connectionConfig.connections) {
        Object.entries(connectionConfig.connections).forEach(([position, pdv]) => {
            console.log(`Configurando PDV na posição ${position}:`, pdv);
            
            // Mapeia IP do PDV para posição no grid
            state.pdvMapping[pdv.pdvIp] = position;
            
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

// Conexão com o servidor WebSocket
function connectToServer(serverUrl) {
    const serverStatus = document.getElementById('serverStatus');
    if (!serverStatus) return;
    
    try {
        console.log(`Iniciando conexão WebSocket com: ws://${serverUrl}`);
        state.pdvConnection = new WebSocket(`ws://${serverUrl}`);
        
        state.pdvConnection.onopen = () => {
            console.log('Conectado ao servidor PDV');
            serverStatus.textContent = 'Conectado ao servidor';
            serverStatus.classList.add('connected');
            state.isConnectedToServer = true;
            
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
        
        state.pdvConnection.onclose = () => {
            console.log('Desconectado do servidor PDV');
            serverStatus.textContent = 'Desconectado do servidor';
            serverStatus.classList.remove('connected');
            state.isConnectedToServer = false;
        };
        
        state.pdvConnection.onerror = (error) => {
            console.error('Erro na conexão com o servidor PDV:', error);
            serverStatus.textContent = 'Erro na conexão com o servidor';
            serverStatus.classList.remove('connected');
            state.isConnectedToServer = false;
        };
        
        // Configura handler para mensagens
        setupMessageHandler();
    } catch (error) {
        console.error('Falha ao conectar ao servidor:', error);
        serverStatus.textContent = 'Falha na conexão com o servidor';
        serverStatus.classList.remove('connected');
    }
}

// Registra PDV no servidor
function registerPDV(position, pdvIp) {
    if (!state.isConnectedToServer || !state.pdvConnection) {
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
        state.pdvConnection.send(JSON.stringify(registerCommand));
    } catch (error) {
        console.error(`Erro ao conectar ao PDV ${position}:`, error);
        logContainer.innerHTML += '[ERRO] Falha na conexão com o PDV\n';
        statusElement.textContent = 'Erro PDV';
    }
}

// Conecta câmera RTSP via WebSocket
function connectCamera(position, rtspUrl, serverUrl) {
    const videoElement = document.getElementById(`remoteVideo${position}`);
    const statusElement = document.getElementById(`status${position}`);
    
    if (!videoElement || !statusElement) {
        console.error(`Elementos de vídeo ou status não encontrados para a posição ${position}`);
        return;
    }
    
    // Fecha conexão existente, se houver
    if (state.rtspWebsockets[position]) {
        state.rtspWebsockets[position].close();
    }
    if (state.peerConnections[position]) {
        state.peerConnections[position].close();
    }
    
    // Atualiza interface
    statusElement.textContent = 'Conectando câmera...';
    
    try {
        console.log(`Iniciando WebSocket para RTSP na posição ${position}: ws://${serverUrl}`);
        // Cria nova conexão WebSocket para RTSP
        state.rtspWebsockets[position] = new WebSocket(`ws://${serverUrl}`);
        
        state.rtspWebsockets[position].onopen = async () => {
            console.log(`WebSocket ${position} conectado. Enviando URL RTSP:`, rtspUrl);
            state.rtspWebsockets[position].send(rtspUrl);
        };
        
        state.rtspWebsockets[position].onmessage = async (event) => {
            try {
                console.log(`Mensagem recebida da câmera ${position}:`, event.data.substring(0, 100) + '...');
                const message = JSON.parse(event.data);
                
                // Verifica se a mensagem contém sdp (oferta SDP)
                if (message.sdp && message.type === 'offer') {
                    console.log(`Recebida oferta SDP para câmera ${position}`);
                    await handleOffer(position, message, videoElement);
                    statusElement.textContent = `Conectado - Câmera ${position}`;
                    statusElement.classList.add('connected');
                } else {
                    console.log(`Câmera ${position} recebeu mensagem:`, message);
                }
            } catch (error) {
                console.error(`Erro ao processar mensagem na câmera ${position}:`, error);
                statusElement.textContent = 'Erro na câmera';
                statusElement.classList.remove('connected');
                statusElement.classList.add('error');
            }
        };
        
        state.rtspWebsockets[position].onclose = () => {
            console.log(`WebSocket ${position} fechado`);
            statusElement.textContent = 'Câmera desconectada';
            statusElement.classList.remove('connected');
        };
        
        state.rtspWebsockets[position].onerror = (error) => {
            console.error(`Erro no WebSocket ${position}:`, error);
            statusElement.textContent = 'Erro na câmera';
            statusElement.classList.remove('connected');
            statusElement.classList.add('error');
        };
    } catch (error) {
        console.error(`Erro ao conectar à câmera ${position}:`, error);
        statusElement.textContent = 'Erro na câmera';
        statusElement.classList.remove('connected');
        statusElement.classList.add('error');
    }
}

// Atualiza o status de um quadrante na UI
function updateStatus(position, message) {
    const statusElement = document.getElementById(`status${position}`);
    if (statusElement) {
        statusElement.textContent = message;
    }
}

// Handler para mensagens do PDV
function setupMessageHandler() {
    if (!state.pdvConnection) {
        console.error("Sem conexão PDV para configurar handler de mensagem");
        return;
    }
    
    state.pdvConnection.onmessage = (event) => {
        try {
            console.log("Mensagem recebida do servidor:", event.data.substring(0, 100) + '...');
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

// Resposta de registro do PDV
function handleRegisterResponse(message) {
    const pdvIp = message.pdv_ip;
    const position = state.pdvMapping[pdvIp];
    
    if (position) {
        const logContainer = document.getElementById(`log${position}`);
        const statusElement = document.getElementById(`status${position}`);
        
        if (message.success) {
            console.log(`Registrado com sucesso para o PDV ${pdvIp}`);
            statusElement.textContent = `Conectado - PDV ${pdvIp}`;
            statusElement.classList.add('connected');
            logContainer.innerHTML += `[INFO] Registrado no PDV ${pdvIp}\n`;
        } else {
            console.log(`Falha ao registrar para o PDV ${pdvIp}`);
            statusElement.textContent = 'Falha - PDV';
            statusElement.classList.remove('connected');
            statusElement.classList.add('error');
            logContainer.innerHTML += `[ERRO] Falha ao registrar no PDV ${pdvIp}\n`;
        }
    }
}

// Processa dados recebidos do PDV
function handlePdvData(message) {
    const pdvIp = message.pdv_ip;
    const position = state.pdvMapping[pdvIp];
    
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
    } else {
        console.warn(`Recebida mensagem do PDV ${pdvIp}, mas não há quadrante associado`);
    }
}

// Processa alertas de inatividade do PDV
function handleInactivityAlert(message) {
    const pdvIp = message.pdv_ip;
    const position = state.pdvMapping[pdvIp];
    
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

// Adiciona alerta de inatividade à fila
function addInactivityAlert(position, pdvIp, inactiveTime) {
    // Se já existe um alerta para esta posição, não faça nada
    if (state.inactivityAlerts.active[position]) {
        return;
    }
    
    // Cria o objeto de alerta
    const alert = {
        position,
        pdvIp,
        inactiveTime
    };
    
    // Adiciona à fila
    state.inactivityAlerts.queue.push(alert);
    
    // Se não há alertas ativos nesta posição, inicia o alerta
    if (!state.inactivityAlerts.active[position]) {
        processNextAlert();
    }
}

// Processa o próximo alerta na fila
function processNextAlert() {
    // Se não há alertas na fila, não faz nada
    if (state.inactivityAlerts.queue.length === 0) {
        return;
    }
    
    // Pega o próximo alerta da fila
    const alert = state.inactivityAlerts.queue.shift();
    
    // Mostra o alerta
    showInactivityAlert(alert);
}

// Mostra alerta de inatividade visualmente
function showInactivityAlert(alert) {
    // Marca como ativo
    state.inactivityAlerts.active[alert.position] = alert;
    
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
    }
}

// Configuração do WebRTC
async function handleOffer(position, offer, videoElement) {
    try {
        // Fecha conexão existente, se houver
        if (state.peerConnections[position]) {
            state.peerConnections[position].close();
        }
        
        console.log(`Criando RTCPeerConnection para câmera ${position}`);
        
        // Cria uma nova conexão RTCPeerConnection
        state.peerConnections[position] = new RTCPeerConnection(iceServers);
        
        // Configura os handlers de eventos
        state.peerConnections[position].ontrack = (event) => {
            if (event.track.kind === 'video') {
                console.log(`Recebido stream de vídeo para câmera ${position}`);
                
                videoElement.srcObject = event.streams[0];
                
                // Adicione eventos para monitorar o estado do vídeo
                videoElement.onloadedmetadata = () => {
                    console.log(`Vídeo metadata carregada para câmera ${position}`);
                    // Tente iniciar a reprodução explicitamente
                    videoElement.play().then(() => {
                        console.log(`Vídeo iniciado com sucesso para câmera ${position}`);
                    }).catch(err => {
                        console.error(`Erro ao iniciar vídeo para câmera ${position}:`, err);
                    });
                };
                
                console.log(`Câmera ${position}: Stream de vídeo conectado`);
                updateStatus(position, `Conectado - Câmera ${position}`);
            }
        };
        
        state.peerConnections[position].onicecandidate = (event) => {
            if (event.candidate === null) {
                // ICE gathering completed, envia a resposta final
                console.log(`ICE gathering completo para câmera ${position}, enviando resposta`);
                sendAnswer(position);
            }
        };
        
        state.peerConnections[position].oniceconnectionstatechange = () => {
            const connectionState = state.peerConnections[position].iceConnectionState;
            console.log(`ICE connection state para câmera ${position}:`, connectionState);
            
            // Atualiza status na interface
            const statusElement = document.getElementById(`status${position}`);
            
            if (!statusElement) return;
            
            if (connectionState === 'connected' || connectionState === 'completed') {
                statusElement.textContent = `Conectado - Câmera ${position}`;
                statusElement.classList.add('connected');
                statusElement.classList.remove('error');
            } else if (connectionState === 'failed' || connectionState === 'disconnected' || connectionState === 'closed') {
                statusElement.textContent = `Câmera ${connectionState}`;
                statusElement.classList.remove('connected');
                if (connectionState === 'failed') {
                    statusElement.classList.add('error');
                }
            }
        };
        
        // Define a oferta remota
        console.log(`Definindo oferta remota para câmera ${position}`);
        await state.peerConnections[position].setRemoteDescription({
            type: offer.type,
            sdp: offer.sdp
        });
        
        // Cria a resposta
        console.log(`Criando resposta para câmera ${position}`);
        const answer = await state.peerConnections[position].createAnswer();
        await state.peerConnections[position].setLocalDescription(answer);
        
        console.log(`Resposta SDP para câmera ${position} criada com sucesso`);
    } catch (error) {
        console.error(`Erro ao processar oferta para câmera ${position}:`, error);
        updateStatus(position, 'Erro WebRTC');
    }
}

// Envia resposta SDP para o servidor
function sendAnswer(position) {
    try {
        if (state.peerConnections[position] && 
            state.peerConnections[position].localDescription && 
            state.rtspWebsockets[position]) {
            
            const answer = {
                type: state.peerConnections[position].localDescription.type,
                sdp: state.peerConnections[position].localDescription.sdp
            };
            
            console.log(`Enviando resposta SDP para câmera ${position}`);
            state.rtspWebsockets[position].send(JSON.stringify(answer));
        } else {
            console.error(`Não foi possível enviar resposta para câmera ${position}: RTCPeerConnection ou WebSocket não disponíveis`);
        }
    } catch (error) {
        console.error(`Erro ao enviar resposta para câmera ${position}:`, error);
    }
}

// Eventos básicos de UI que não podem ser facilmente migrados para Livewire
function initializeInterfaceEvents() {
    // Duplo clique para fullscreen
    const quadrants = document.querySelectorAll('.stream-container');
    quadrants.forEach(quadrant => {
        quadrant.addEventListener('dblclick', function() {
            toggleQuadrantFullscreen(this);
        });
    });
    
    // Botão de fullscreen do navegador
    const fullscreenBtn = document.getElementById('browserFullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function() {
            toggleBrowserFullscreen();
        });
    }
    
    // Escuta tecla ESC para sair do fullscreen
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            // Sai do fullscreen de quadrante com ESC
            const fullscreenQuadrant = document.querySelector('.stream-container.fullscreen');
            if (fullscreenQuadrant) {
                toggleQuadrantFullscreen(fullscreenQuadrant);
            }
            // Também sai do modo F11 fullscreen com ESC
            else if (state.isFullScreenMode) {
                exitBrowserFullscreen();
            }
        } else if (event.key === 'F11') {
            event.preventDefault();
            toggleBrowserFullscreen();
        }
    });
    
    // Eventos de mudança de fullscreen do navegador
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);
}

// Função para alternar fullscreen de um quadrante específico
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
        
        // Esconde a mensagem de instrução
        const instruction = document.getElementById('fullscreen-instruction');
        if (instruction) {
            instruction.style.display = 'none';
        }
        
        // Restaura o estado original do sidebar
        if (state.originalSidebarCollapsed) {
            document.body.classList.add('sidebar-collapse');
        }
    } else {
        // Guarda o estado original do sidebar
        state.originalSidebarCollapsed = document.body.classList.contains('sidebar-collapse');
        
        // Se o sidebar estiver colapsado, expande-o para o modo fullscreen
        if (state.originalSidebarCollapsed) {
            document.body.classList.remove('sidebar-collapse');
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
        
        // Mostra a mensagem de instrução
        const instruction = document.getElementById('fullscreen-instruction');
        if (instruction) {
            instruction.style.display = 'block';
        }
    }
}

// Função para alternar fullscreen do navegador
function toggleBrowserFullscreen() {
    if (!state.isFullScreenMode) {
        enterBrowserFullscreen();
    } else {
        exitBrowserFullscreen();
    }
}

// Entra em fullscreen do navegador
function enterBrowserFullscreen() {
    // Guarda o estado original do sidebar
    state.originalSidebarCollapsed = document.body.classList.contains('sidebar-collapse');
    
    // Se o sidebar estiver colapsado, expande-o
    if (state.originalSidebarCollapsed) {
        document.body.classList.remove('sidebar-collapse');
    }
    
    const docElm = document.documentElement;
    
    try {
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
        } else if (docElm.mozRequestFullScreen) {
            docElm.mozRequestFullScreen();
        } else if (docElm.webkitRequestFullscreen) {
            docElm.webkitRequestFullscreen();
        } else if (docElm.msRequestFullscreen) {
            docElm.msRequestFullscreen();
        } else {
            console.error('Nenhum método de fullscreen suportado');
        }
    } catch (error) {
        console.error('Erro ao tentar entrar em fullscreen:', error);
    }
    
    document.body.classList.add('browser-fullscreen');
    state.isFullScreenMode = true;
    
    // Atualiza o botão de controle
    const fullscreenBtn = document.getElementById('browserFullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i> Sair da Tela Cheia';
    }
}

// Sai do fullscreen do navegador
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
    state.isFullScreenMode = false;
    
    // Restaura o estado original do sidebar
    if (state.originalSidebarCollapsed) {
        document.body.classList.add('sidebar-collapse');
    }
    
    // Atualiza o botão de controle
    const fullscreenBtn = document.getElementById('browserFullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.innerHTML = '<i class="fas fa-desktop"></i> Tela Cheia (F11)';
    }
}

// Gerencia mudanças de fullscreen do navegador
function handleFullscreenChange() {
    const isFullscreen = !!(document.fullscreenElement || 
                           document.webkitFullscreenElement || 
                           document.mozFullScreenElement || 
                           document.msFullscreenElement);
    
    // Atualiza o estado baseado na mudança externa
    if (isFullscreen !== state.isFullScreenMode) {
        if (isFullscreen) {
            // Se entrarmos em fullscreen, armazena o estado do sidebar e o expande
            if (!state.isFullScreenMode) {
                state.originalSidebarCollapsed = document.body.classList.contains('sidebar-collapse');
                if (state.originalSidebarCollapsed) {
                    document.body.classList.remove('sidebar-collapse');
                }
            }
            
            document.body.classList.add('browser-fullscreen');
            state.isFullScreenMode = true;
        } else {
            document.body.classList.remove('browser-fullscreen');
            state.isFullScreenMode = false;
            
            // Restaura o estado original do sidebar ao sair do fullscreen
            if (state.originalSidebarCollapsed) {
                document.body.classList.add('sidebar-collapse');
            }
        }
        
        // Atualiza botão
        const fullscreenBtn = document.getElementById('browserFullscreenBtn');
        if (fullscreenBtn) {
            fullscreenBtn.innerHTML = isFullscreen ? 
                '<i class="fas fa-compress"></i> Sair da Tela Cheia' : 
                '<i class="fas fa-desktop"></i> Tela Cheia (F11)';
        }
    }
}

// Expor funções globais necessárias
window.toggleBrowserFullscreen = toggleBrowserFullscreen;