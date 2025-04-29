import { state } from './config.js';
import { 
    toggleQuadrantFullscreen, 
    toggleBrowserFullscreen, 
    handleFullscreenChange,
    exitBrowserFullscreen 
} from './fullscreen.js';

export function initializeInterfaceEvents() {
    // Adiciona event listeners de duplo clique para todos os quadrantes
    const quadrants = document.querySelectorAll('.stream-container');
    quadrants.forEach(quadrant => {
        quadrant.addEventListener('dblclick', function() {
            toggleQuadrantFullscreen(this);
        });
    });
    
    // Adiciona listener para teclas F11 e Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'F11') {
            event.preventDefault();
            toggleBrowserFullscreen();
        } else if (event.key === 'Escape') {
            // Sai do fullscreen de quadrante com ESC
            const fullscreenQuadrant = document.querySelector('.stream-container.fullscreen');
            if (fullscreenQuadrant) {
                toggleQuadrantFullscreen(fullscreenQuadrant);
            }
            // Também sai do modo F11 fullscreen com ESC
            else if (state.isFullScreenMode) {
                exitBrowserFullscreen();
            }
        }
    });
    
    // Adiciona listeners para eventos de fullscreen do navegador
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);
    
    // Adiciona compatibilidade específica para AdminLTE
    initializeAdminLteCompatibility();
}

function initializeAdminLteCompatibility() {
    // Observer para monitorar mudanças na classe do body (para detectar alterações do sidebar)
    const bodyObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                // Se estamos em modo fullscreen, assegura que o sidebar está expandido, tava bugando com ele collapsed
                if (document.body.classList.contains('browser-fullscreen')) {
                    if (document.body.classList.contains('sidebar-collapse')) {
                        document.body.classList.remove('sidebar-collapse');
                    }
                    
                    // Força esconder elementos do AdminLTE
                    const sidebar = document.querySelector('.main-sidebar');
                    if (sidebar) {
                        sidebar.style.width = '0';
                        sidebar.style.display = 'none';
                        sidebar.style.left = '-280px';
                    }
                    
                    // Ajusta content-wrapper
                    const contentWrapper = document.querySelector('.content-wrapper');
                    if (contentWrapper) {
                        contentWrapper.style.marginLeft = '0';
                        contentWrapper.style.width = '100%';
                    }
                }
            }
        });
    });
    
    bodyObserver.observe(document.body, { attributes: true });
    
    const pushMenuToggleButton = document.querySelector('[data-widget="pushmenu"]');
    if (pushMenuToggleButton) {
        pushMenuToggleButton.addEventListener('click', function() {
            // Se estamos em modo fullscreen, previne a ação padrão
            if (document.body.classList.contains('browser-fullscreen')) {
                // Força o sidebar expandido (remove a classe sidebar-collapse)
                document.body.classList.remove('sidebar-collapse');
                
                // Força esconder o sidebar
                const sidebar = document.querySelector('.main-sidebar');
                if (sidebar) {
                    sidebar.style.width = '0';
                    sidebar.style.display = 'none';
                    sidebar.style.left = '-280px';
                }
                
                // Força o conteúdo a ocupar toda a largura
                const contentWrapper = document.querySelector('.content-wrapper');
                if (contentWrapper) {
                    contentWrapper.style.marginLeft = '0';
                    contentWrapper.style.width = '100%';
                }
            }
        });
    }
}