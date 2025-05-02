// Estado global mínimo necessário
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
    connectToServer(connectionConfig.serverUrls.pdv);
    
    // Para cada PDV, inicializa conexões
    if (connectionConfig.connections) {
        Object.entries(connectionConfig.connections).forEach(([position, pdv]) => {
            // Inicializa conexão com a câmera
            if (pdv.rtspUrl) {
                connectCamera(position, pdv.rtspUrl, connectionConfig.serverUrls.rtsp);
            } else {
                Livewire.emit('updateStatus', position, '', 'error');
            }
        });
    }
}

// Conexão com o servidor WebSocket
function connectToServer(serverUrl) {
    try {
        state.pdvConnection = new WebSocket(`ws://${serverUrl}`);
        
        state.pdvConnection.onopen = () => {
            Livewire.emit('serverConnectionStatusChanged', true);
            state.isConnectedToServer = true;
            
            // Registra todos os PDVs agora que estamos conectados
            if (window.monitorConfig && window.monitorConfig.connectionConfig) {
                Object.entries(window.monitorConfig.connectionConfig.connections).forEach(([position, pdv]) => {
                    if (pdv.pdvIp) {
                        registerPDV(position, pdv.pdvIp);
                    }
                });
            }
        };
        
        state.pdvConnection.onclose = () => {
            Livewire.emit('serverConnectionStatusChanged', false);
            state.isConnectedToServer = false;
        };
        
        state.pdvConnection.onerror = () => {
            Livewire.emit('serverConnectionStatusChanged', false, 'error');
            state.isConnectedToServer = false;
        };
        
        // Configura handler para mensagens
        setupMessageHandler();
    } catch (error) {
        Livewire.emit('serverConnectionStatusChanged', false, 'error');
    }
}

// Registra PDV no servidor
function registerPDV(position, pdvIp) {
    if (!state.isConnectedToServer || !state.pdvConnection) {
        return;
    }
    
    // Notifica Livewire sobre tentativa de conexão
    // Livewire.emit('pdvConnectionAttempt', position, pdvIp);
    
    // Envia comando de registro para o PDV
    try {
        const registerCommand = {
            command: "register",
            pdv_ip: pdvIp
        };
        
        state.pdvConnection.send(JSON.stringify(registerCommand));
    } catch (error) {
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
    Livewire.emit('updateStatus', position, '', '');
    
    try {
        // Cria nova conexão WebSocket para RTSP
        state.rtspWebsockets[position] = new WebSocket(`ws://${serverUrl}`);
        
        state.rtspWebsockets[position].onopen = async () => {
            state.rtspWebsockets[position].send(rtspUrl);
        };
        
        state.rtspWebsockets[position].onmessage = async (event) => {
            try {
                const message = JSON.parse(event.data);
                
                // Verifica se a mensagem contém sdp (oferta SDP)
                if (message.sdp && message.type === 'offer') {
                    const videoElement = document.getElementById(`remoteVideo${position}`);
                    if (videoElement) {
                        await handleOffer(position, message, videoElement);
                        Livewire.emit('updateStatus', position, '', 'connected');
                    }
                }
            } catch (error) {
                Livewire.emit('updateStatus', position, '', 'error');
            }
        };
        
        state.rtspWebsockets[position].onclose = () => {
            Livewire.emit('updateStatus', position, '', '');
        };
        
        state.rtspWebsockets[position].onerror = () => {
            Livewire.emit('updateStatus', position, '', 'error');
        };
    } catch (error) {
        Livewire.emit('updateStatus', position, '', 'error');
    }
}

// Handler para mensagens do PDV
function setupMessageHandler() {
    if (!state.pdvConnection) {
        return;
    }
    
    state.pdvConnection.onmessage = (event) => {
        try {
            const message = JSON.parse(event.data);
            
            // Se for uma resposta de registro, processa
            // if (message.type === 'register_response') {
            //     Livewire.emit('handleRegisterResponse', message.pdv_ip, message.success);
            // }
            // Se for dados do PDV, exibe no log do quadrante correspondente
            if (message.type === 'pdv_data') {
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
        
        // Cria uma nova conexão RTCPeerConnection
        state.peerConnections[position] = new RTCPeerConnection(iceServers);
        
        // Configura os handlers de eventos
        state.peerConnections[position].ontrack = (event) => {
            if (event.track.kind === 'video') {
                videoElement.srcObject = event.streams[0];
                
                // Adicione eventos para monitorar o estado do vídeo
                videoElement.onloadedmetadata = () => {
                    videoElement.play().catch(() => {});
                };
                
                Livewire.emit('updateStatus', position, '', 'connected');
            }
        };
        
        state.peerConnections[position].onicecandidate = (event) => {
            if (event.candidate === null) {
                // ICE gathering completed, envia a resposta final
                sendAnswer(position);
            }
        };
        
        state.peerConnections[position].oniceconnectionstatechange = () => {
            const connectionState = state.peerConnections[position].iceConnectionState;
            
            // Delegamos atualização de status para o Livewire
            if (connectionState === 'connected' || connectionState === 'completed') {
                Livewire.emit('updateStatus', position, '', 'connected');
            } else if (connectionState === 'failed' || connectionState === 'disconnected' || connectionState === 'closed') {
                const statusClass = connectionState === 'failed' ? 'error' : '';
                Livewire.emit('updateStatus', position, '', statusClass);
            }
        };
        
        // Define a oferta remota
        await state.peerConnections[position].setRemoteDescription({
            type: offer.type,
            sdp: offer.sdp
        });
        
        // Cria a resposta
        const answer = await state.peerConnections[position].createAnswer();
        await state.peerConnections[position].setLocalDescription(answer);
    } catch (error) {
        Livewire.emit('updateStatus', position, '', 'error');
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
            
            state.rtspWebsockets[position].send(JSON.stringify(answer));
        }
    } catch (error) {
        console.error(`Erro ao enviar resposta:`, error);
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