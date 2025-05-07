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
document.addEventListener('DOMContentLoaded', function() {
    
    // Sistema de fila de alertas
    if (!window.alertSystem) {
        window.alertSystem = {
            queue: [],           // Fila de posições com alertas pendentes
            activeAlert: null,   // Alerta atualmente sendo exibido em fullscreen
            alertingLogs: new Set() // Mantemos o set para rastrear todos os logs em alerta
        };
    }
    
    // Função para parar um alerta específico
    window.stopAlert = function(position) {
        // Remove a classe de animação
        const logContainer = document.getElementById(`log${position}`);
        if (logContainer) {
            logContainer.classList.remove('inactivity-alert-blink');
        }

        // Marcar o alerta como resolvido no banco de dados
        if (window.alertSystem.alertIds && window.alertSystem.alertIds[position]) {
            fetch('/alertas/resolver', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    alerta_id: window.alertSystem.alertIds[position]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    delete window.alertSystem.alertIds[position];
                }
            })
            .catch(error => {
                console.error('Erro ao resolver alerta:', error);
            });
        }
        
        // Remove da lista de alertas ativos
        window.alertSystem.alertingLogs.delete(position);
        
        // Se este era o alerta ativo no momento, fecha o fullscreen
        if (window.alertSystem.activeAlert === position) {
            window.alertSystem.activeAlert = null;
            
            // Sai do modo fullscreen do quadrante
            const quadrant = document.getElementById(`quadrant${position}`);
            if (quadrant && quadrant.classList.contains('fullscreen')) {
                toggleQuadrantFullscreen(quadrant);
            }
            
            // Processa o próximo alerta da fila, se houver
            setTimeout(processNextAlert, 500);
        }
        
        // Se o alerta está na fila, remove-o
        window.alertSystem.queue = window.alertSystem.queue.filter(pos => pos !== position);
    };
    
    // Função para processar o próximo alerta da fila
    function processNextAlert() {
        // Se já temos um alerta ativo ou a fila está vazia, não fazemos nada
        if (window.alertSystem.activeAlert !== null || window.alertSystem.queue.length === 0) {
            return;
        }
        
        // Pegamos o próximo alerta da fila
        const nextPosition = window.alertSystem.queue.shift();
        
        // Definimos como alerta ativo
        window.alertSystem.activeAlert = nextPosition;
        
        // Colocamos o quadrante em fullscreen
        const quadrant = document.getElementById(`quadrant${nextPosition}`);
        if (quadrant && !quadrant.classList.contains('fullscreen')) {
            toggleQuadrantFullscreen(quadrant);
        }
    }
    
    // Função para adicionar um alerta e processá-lo
    function addAlert(position) {
        // Adiciona à lista de logs com alerta (para o efeito visual)
        window.alertSystem.alertingLogs.add(position);
        
        // Aplica a classe de animação
        const logContainer = document.getElementById(`log${position}`);
        if (logContainer) {
            logContainer.classList.add('inactivity-alert-blink');
        }
        
        // Obter dados do PDV da configuração
        const pdvData = window.monitorConfig.connectionConfig.connections[position];

        // Registrar o alerta no banco de dados
        if (pdvData) {
            // Enviar requisição para o backend
            fetch('/alertas/registrar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    pdv_codigo: pdvData.pdvCode || pdvData.selfId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Armazenar o ID do alerta para resolver posteriormente
                    if (!window.alertSystem.alertIds) window.alertSystem.alertIds = {};
                    window.alertSystem.alertIds[position] = data.alerta_id;
                }
            })
            .catch(error => {
                console.error('Erro ao registrar alerta:', error);
            });
        }

        // Se já temos um alerta ativo, adiciona à fila
        if (window.alertSystem.activeAlert !== null) {
            if (!window.alertSystem.queue.includes(position)) {
                window.alertSystem.queue.push(position);
            }
            return;
        }
        
        // Se não temos alerta ativo, este se torna o ativo
        window.alertSystem.activeAlert = position;
        
        // Coloca o quadrante em fullscreen
        const quadrant = document.getElementById(`quadrant${position}`);
        if (quadrant && !quadrant.classList.contains('fullscreen')) {
            toggleQuadrantFullscreen(quadrant);
        }
    }
    
    // Reaplicamos alertas visuais após atualizações do DOM
    function applyAlerts() {
        window.alertSystem.alertingLogs.forEach(position => {
            const logContainer = document.getElementById(`log${position}`);
            if (logContainer) {
                logContainer.classList.add('inactivity-alert-blink');
            }
        });
    }
    
    // Listener para o evento de inatividade
    window.addEventListener('inactivity-alert', function(e) {
        const { position } = e.detail;
        addAlert(position);
    });
    
    // Módificamos a função de toggle para integrar com o sistema de fila
    window.originalToggleQuadrantFullscreen = window.toggleQuadrantFullscreen;
    window.toggleQuadrantFullscreen = function(element) {
        // Obter o ID do quadrante
        const positionMatch = element.id.match(/quadrant(\d+)/);
        const position = positionMatch ? parseInt(positionMatch[1]) : null;
        
        // Se estamos saindo do fullscreen em um alerta ativo
        if (element.classList.contains('fullscreen') && 
            position && window.alertSystem.activeAlert === position) {
            
            // Paramos o alerta atual
            window.stopAlert(position);
            // Não precisamos chamar processNextAlert() aqui porque stopAlert() já faz isso
        } else {
            // Comportamento padrão para outros casos
            window.originalToggleQuadrantFullscreen(element);
        }
    };
    
    // Após atualização do Livewire, reaplicamos os alertas visuais
    Livewire.hook('message.processed', (message, component) => {
        applyAlerts();
    });

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
    // Obter o ID do quadrante
    const positionMatch = element.id.match(/quadrant(\d+)/);
    const position = positionMatch ? parseInt(positionMatch[1]) : null;
    
    // Se o quadrante estiver em alerta e estamos saindo do fullscreen
    if (position && window.alertSystem && 
        window.alertSystem.alertingLogs.has(position) && 
        element.classList.contains('fullscreen')) {
        
        // Removemos apenas o efeito visual, sem afetar a fila se for apenas clique de fechamento
        const logContainer = document.getElementById(`log${position}`);
        if (logContainer) {
            logContainer.classList.remove('inactivity-alert-blink');
        }
        
        // Remove da lista de alertas ativos
        window.alertSystem.alertingLogs.delete(position);
        
        // Se este era o alerta ativo no momento
        if (window.alertSystem.activeAlert === position) {
            // Salvamos que não temos mais alerta ativo
            window.alertSystem.activeAlert = null;
            
            // Remove este alerta da fila (caso esteja lá também)
            window.alertSystem.queue = window.alertSystem.queue.filter(pos => pos !== position);
            
            // Definimos um timeout para processar o próximo alerta depois que este quadrante fechar
            setTimeout(function() {
                // Processamos o próximo alerta da fila, se houver
                if (window.alertSystem.queue.length > 0) {
                    const nextPosition = window.alertSystem.queue.shift();
                    window.alertSystem.activeAlert = nextPosition;
                    
                    // Colocamos o próximo quadrante em fullscreen
                    const nextQuadrant = document.getElementById(`quadrant${nextPosition}`);
                    if (nextQuadrant) {
                        // Usamos o toggleQuadrantFullscreen original para evitar recursão
                        const allQuadrants = document.querySelectorAll('.stream-container');
                        
                        // Guarda o estado original do sidebar
                        state.originalSidebarCollapsed = document.body.classList.contains('sidebar-collapse');
                        
                        // Se o sidebar estiver colapsado, expande-o para o modo fullscreen
                        if (state.originalSidebarCollapsed) {
                            document.body.classList.remove('sidebar-collapse');
                        }
                        
                        // Salva o estado de exibição atual de todos os quadrantes
                        allQuadrants.forEach(quadrant => {
                            if (quadrant !== nextQuadrant) {
                                quadrant.setAttribute('data-original-display', quadrant.style.display || 'flex');
                                quadrant.style.display = 'none';
                            }
                        });
                        
                        // Entra no modo fullscreen do quadrante
                        nextQuadrant.classList.add('fullscreen');
                        
                        // Mostra a mensagem de instrução
                        const instruction = document.getElementById('fullscreen-instruction');
                        if (instruction) {
                            instruction.style.display = 'block';
                        }
                        
                        // Notifica o Livewire da mudança
                        Livewire.emit('quadrantFullscreenChanged', nextPosition);
                    }
                }
            }, 300); // Pequeno atraso para garantir que a transição do quadrante atual seja concluída
        }
    }
    
    // Resto do código existente para alternar fullscreen
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
        if (position) {
            Livewire.emit('quadrantFullscreenChanged', position);
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