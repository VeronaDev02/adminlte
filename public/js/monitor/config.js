// Estado global compartilhado entre módulos
export const state = {
    // Conexões e gerenciamento
    rtspWebsockets: {},
    pdvConnection: null,
    peerConnections: {},
    isConnectedToServer: false,
    pdvMapping: {},
    
    // Sistema de alertas
    inactivityAlerts: { 
        queue: [], 
        active: {} 
    },
    
    // Configuração de UI
    isFullScreenMode: false,
    originalSidebarCollapsed: false
};

// Configurações de ICE para WebRTC
export const iceServers = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};