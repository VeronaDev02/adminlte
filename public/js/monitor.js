// monitor.js - Refatorado para integração com Livewire

// Estado global mínimo necessário (reduzido)
const state = {
    rtspWebsockets: {},
    pdvConnection: null,
    peerConnections: {},
    isConnectedToServer: false,
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
    
    // Para cada PDV, inicializa conexões
    if (connectionConfig.connections) {
        Object.entries(connectionConfig.connections).forEach(([position, pdv]) => {
            console.log(`Configurando PDV na posição ${position}:`, pdv);
            
            // Inicializa conexão com a câmera
            if (pdv.rtspUrl) {
                console.log(`Iniciando conexão com a câmera ${position} URL: ${pdv.rtspUrl}`);
                connectCamera(position, pdv.rtspUrl, connectionConfig.serverUrls.rtsp);
            } else {
                console.warn(`Sem URL RTSP para a posição ${position}`);
                // Utilizamos evento Livewire para atualizar status
                Livewire.emit('updateStatus', position, 'Sem URL RTSP');
            }
        });
    }
}

// Conexão com o servidor WebSocket
function connectToServer(serverUrl) {
    try {
        console.log(`Iniciando conexão WebSocket com: ws://${serverUrl}`);
        state.pdvConnection = new WebSocket(`ws://${serverUrl}`);
        
        state.pdvConnection.onopen = () => {
            console.log('Conectado ao servidor PDV');
            // Delegamos a atualização de status para o Livewire
            Livewire.emit('serverConnectionStatusChanged', true);
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
            Livewire.emit('serverConnectionStatusChanged', false);
            state.isConnectedToServer = false;
        };
        
        state.pdvConnection.onerror = (error) => {
            console.error('Erro na conexão com o servidor PDV:', error);
            Livewire.emit('serverConnectionStatusChanged', false, 'error');
            state.isConnectedToServer = false;
        };
        
        // Configura handler para mensagens
        setupMessageHandler();
    } catch (error) {
        console.error('Falha ao conectar ao servidor:', error);
        Livewire.emit('serverConnectionStatusChanged', false, 'error');
    }
}

// Registra PDV no servidor
function registerPDV(position, pdvIp) {
    if (!state.isConnectedToServer || !state.pdvConnection) {
        console.error('Não conectado ao servidor. Impossível registrar PDV.');
        return;
    }
    
    // Notifica Livewire sobre tentativa de conexão
    Livewire.emit('pdvConnectionAttempt', position, pdvIp);
    
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
        Livewire.emit('pdvConnectionError', position, pdvIp, error.message);
    }
}

// Conecta câmera RTSP via WebSocket
function connectCamera(position, rtspUrl, serverUrl) {
    // Fecha conexão existente, se houver
    if (state.rtspWebsockets[position]) {
        state.rtspWebsockets[position].close();
    }
    if (state.peerConnections[position]) {
        state.peerConnections[position].close();
    }
    
    // Atualiza interface via Livewire
    Livewire.emit('updateStatus', position, 'Conectando câmera...');
    
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
                    const videoElement = document.getElementById(`remoteVideo${position}`);
                    if (videoElement) {
                        await handleOffer(position, message, videoElement);
                        Livewire.emit('updateStatus', position, `Conectado - Câmera ${position}`, 'connected');
                    }
                } else {
                    console.log(`Câmera ${position} recebeu mensagem:`, message);
                }
            } catch (error) {
                console.error(`Erro ao processar mensagem na câmera ${position}:`, error);
                Livewire.emit('updateStatus', position, 'Erro na câmera', 'error');
            }
        };
        
        state.rtspWebsockets[position].onclose = () => {
            console.log(`WebSocket ${position} fechado`);
            Livewire.emit('updateStatus', position, 'Câmera desconectada');
        };
        
        state.rtspWebsockets[position].onerror = (error) => {
            console.error(`Erro no WebSocket ${position}:`, error);
            Livewire.emit('updateStatus', position, 'Erro na câmera', 'error');
        };
    } catch (error) {
        console.error(`Erro ao conectar à câmera ${position}:`, error);
        Livewire.emit('updateStatus', position, 'Erro na câmera', 'error');
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
                // Delegamos ao Livewire
                Livewire.emit('handleRegisterResponse', message.pdv_ip, message.success);
            }
            // Se for dados do PDV, exibe no log do quadrante correspondente
            else if (message.type === 'pdv_data') {
                // Delegamos a manipulação de dados ao Livewire
                Livewire.emit('handlePdvData', message.pdv_ip, message.data);
            }
            // Se for alerta de inatividade do PDV
            else if (message.type === 'pdv_inativo_timeout') {
                // Delegamos ao Livewire
                Livewire.emit('handleInactivityAlert', message.pdv_ip, message.inactive_time);
            }
        } catch (error) {
            console.error('Erro ao processar mensagem do PDV:', error);
        }
    };
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
                Livewire.emit('updateStatus', position, `Conectado - Câmera ${position}`, 'connected');
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
            
            // Delegamos atualização de status para o Livewire
            if (connectionState === 'connected' || connectionState === 'completed') {
                Livewire.emit('updateStatus', position, `Conectado - Câmera ${position}`, 'connected');
            } else if (connectionState === 'failed' || connectionState === 'disconnected' || connectionState === 'closed') {
                const statusClass = connectionState === 'failed' ? 'error' : '';
                Livewire.emit('updateStatus', position, `Câmera ${connectionState}`, statusClass);
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
        Livewire.emit('updateStatus', position, 'Erro WebRTC', 'error');
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

// Eventos de interface que utilizam Alpine.js, mantidos no JS para manipulação DOM específica
function initializeInterfaceEvents() {
    // Duplo clique para fullscreen
    const quadrants = document.querySelectorAll('.stream-container');
    quadrants.forEach(quadrant => {
        quadrant.addEventListener('dblclick', function() {
            toggleQuadrantFullscreen(this);
        });
    });
    
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
    
    // Eventos Livewire para integração bidirecional
    Livewire.on('reconnectCamera', (position, rtspUrl, serverUrl) => {
        connectCamera(position, rtspUrl, serverUrl);
    });
    
    Livewire.on('reconnectPdv', (position, pdvIp) => {
        registerPDV(position, pdvIp);
    });
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
        
        // Notifica o Livewire
        Livewire.emit('quadrantFullscreenChanged', null);
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
        
        // Notifica o Livewire da mudança
        const positionMatch = element.id.match(/quadrant(\d+)/);
        if (positionMatch && positionMatch[1]) {
            Livewire.emit('quadrantFullscreenChanged', positionMatch[1]);
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
    
    // Notifica o Livewire
    Livewire.emit('browserFullscreenChanged', true);
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
    
    // Notifica o Livewire
    Livewire.emit('browserFullscreenChanged', false);
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
        
        // Notifica o Livewire da mudança
        Livewire.emit('browserFullscreenChanged', isFullscreen);
    }
}
function scrollLogsToBottom() {
    // Seleciona todos os containers de log
    const logContainers = document.querySelectorAll('.log-container');
    
    // Para cada container, rola para o fundo
    logContainers.forEach(container => {
        container.scrollTop = container.scrollHeight;
    });
}

Livewire.on('logsUpdated', () => {
    // Pequeno atraso para garantir que o DOM foi atualizado
    setTimeout(scrollLogsToBottom, 50);
});

// Expor funções globais necessárias
window.toggleBrowserFullscreen = toggleBrowserFullscreen;
window.toggleQuadrantFullscreen = toggleQuadrantFullscreen;
window.scrollLogsToBottom = scrollLogsToBottom;