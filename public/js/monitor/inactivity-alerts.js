import { state } from './config.js';

export function addInactivityAlert(position, pdvIp, inactiveTime) {
    // Se já existe um alerta para esta posição, não faça nada
    if (state.inactivityAlerts.active[position]) {
        // console.log(`Alerta já ativo para o quadrante ${position}`);
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

export function processNextAlert() {
    // Se não há alertas na fila, não faz nada
    if (state.inactivityAlerts.queue.length === 0) {
        return;
    }
    
    // Pega o próximo alerta da fila
    const alert = state.inactivityAlerts.queue.shift();
    
    // Mostra o alerta
    showInactivityAlert(alert);
}

export function showInactivityAlert(alert) {
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
        
        // console.log(`Iniciado alerta visual para quadrante ${alert.position}`);
    }
}