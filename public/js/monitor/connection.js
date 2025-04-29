import { state } from './config.js';
import { setupMessageHandler } from './pdv-handler.js';
import { connectCamera } from './rtsp-handler.js';

export function initializeConnections(monitorConfig) {
    console.log("Inicializando conexões com config:", monitorConfig);
    const { connectionConfig, pdvData } = monitorConfig;
    
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

export function connectToServer(serverUrl) {
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

export function registerPDV(position, pdvIp) {
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

export function updateStatus(position, message) {
    const statusElement = document.getElementById(`status${position}`);
    if (statusElement) {
        statusElement.textContent = message;
    }
}