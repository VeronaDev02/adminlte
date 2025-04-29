import { state } from './config.js';
import { handleOffer } from './webrtc-handler.js';
import { updateStatus } from './connection.js';

export function connectCamera(position, rtspUrl, serverUrl) {
    console.log(`DEBUG: Tentando conectar câmera na posição ${position}`);
    console.log(`DEBUG: URL RTSP completa: ${rtspUrl}`);
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
                } else {
                    console.log(`Câmera ${position} recebeu mensagem:`, message);
                }
            } catch (error) {
                console.error(`Erro ao processar mensagem na câmera ${position}:`, error);
                statusElement.textContent = 'Erro na câmera';
            }
        };
        
        state.rtspWebsockets[position].onclose = () => {
            console.log(`WebSocket ${position} fechado`);
            statusElement.textContent = 'Câmera desconectada';
        };
        
        state.rtspWebsockets[position].onerror = (error) => {
            console.error(`Erro no WebSocket ${position}:`, error);
            statusElement.textContent = 'Erro na câmera';
        };
    } catch (error) {
        console.error(`Erro ao conectar à câmera ${position}:`, error);
        statusElement.textContent = 'Erro na câmera';
    }
}