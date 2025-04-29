import { state } from './config.js';
console.log('Config importado:', state);

import { initializeConnections } from './connection.js';
console.log('Connection importado');

import { initializeInterfaceEvents } from './ui.js';
console.log('UI importado');

import { toggleBrowserFullscreen } from './fullscreen.js';
console.log('Fullscreen importado');

// Inicializa quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // console.log("DOM carregado, verificando configuração:", window.monitorConfig);
    
    // Verifica se a configuração está disponível
    if (window.monitorConfig) {
        initializeMonitor();
    } else {
        console.error("Configuração não disponível");
    }
    
    // Inicializa os eventos de interface
    initializeInterfaceEvents();

    // Removido o listener de evento personalizado
});

function initializeMonitor() {
    // Inicializa conexões com base na configuração global
    initializeConnections(window.monitorConfig);
}