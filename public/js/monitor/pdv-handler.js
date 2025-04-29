import { state } from './config.js';
import { addInactivityAlert } from './inactivity-alerts.js';

export function setupMessageHandler() {
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

export function handleRegisterResponse(message) {
    const pdvIp = message.pdv_ip;
    const position = state.pdvMapping[pdvIp];
    
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

export function handlePdvData(message) {
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

export function handleInactivityAlert(message) {
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