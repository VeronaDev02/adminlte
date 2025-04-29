import { state, iceServers } from './config.js';
import { updateStatus } from './connection.js';

export async function handleOffer(position, offer, videoElement) {
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
                console.log(`DEBUG: Recebido stream de vídeo para câmera ${position}`);
                
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
            } else if (connectionState === 'failed' || connectionState === 'disconnected' || connectionState === 'closed') {
                statusElement.textContent = `Câmera ${connectionState}`;
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

export function sendAnswer(position) {
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