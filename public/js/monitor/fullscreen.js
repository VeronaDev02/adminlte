import { state } from './config.js';

export function toggleQuadrantFullscreen(element) {
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
        
        // Restaura o tamanho original e estilos - limpa todos os estilos inline
        element.style = '';
        
        // Restaura os estilos dos containers internos - limpa todos os estilos inline
        const videoContainer = element.querySelector('.video-container');
        const logContainer = element.querySelector('.log-container');
        
        if (videoContainer) {
            videoContainer.style = '';
        }
        
        if (logContainer) {
            logContainer.style = '';
        }
        
        // Esconde a mensagem de instrução
        const instruction = document.getElementById('fullscreen-instruction');
        if (instruction) {
            instruction.style.display = 'none';
        }
        
        // Restaura o estado original do sidebar
        if (state.originalSidebarCollapsed) {
            document.body.classList.add('sidebar-collapse');
            // console.log('Sidebar colapsado para retornar ao estado original');
        }
    } else {
        // Guarda o estado original do sidebar
        state.originalSidebarCollapsed = document.body.classList.contains('sidebar-collapse');
        
        // Se o sidebar estiver colapsado, expande-o para o modo fullscreen
        if (state.originalSidebarCollapsed) {
            document.body.classList.remove('sidebar-collapse');
            // console.log('Sidebar expandido para entrar em modo fullscreen de quadrante');
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
        
        // Força o posicionamento e tamanho corretos
        element.style.position = 'fixed';
        element.style.top = '0';
        element.style.left = '0';
        element.style.right = '0';
        element.style.bottom = '0';
        element.style.width = '100vw';
        element.style.height = '100vh';
        element.style.maxWidth = 'none'; // Em vez de 100vw
        element.style.minWidth = '100vw';
        element.style.maxHeight = 'none'; // Em vez de 100vh
        element.style.minHeight = '100vh';
        element.style.margin = '0';
        element.style.padding = '0';
        element.style.zIndex = '9999';
        element.style.boxSizing = 'border-box';
        element.style.transform = 'none';
        element.style.border = 'none';
        element.style.borderRadius = '0';
        element.style.overflow = 'hidden';
        
        // Ajusta os containers internos para garantir proporções corretas
        const videoContainer = element.querySelector('.video-container');
        const logContainer = element.querySelector('.log-container');
        
        if (videoContainer) {
            videoContainer.style.flex = '0 0 70%';
            videoContainer.style.width = '70%';
            videoContainer.style.maxWidth = '70%';
            videoContainer.style.minWidth = '70%';
            videoContainer.style.height = '100vh';
            videoContainer.style.margin = '0';
            videoContainer.style.padding = '0';
            videoContainer.style.overflow = 'hidden';
        }
        
        if (logContainer) {
            logContainer.style.flex = '0 0 30%';
            logContainer.style.width = '30%';
            logContainer.style.maxWidth = '30%';
            logContainer.style.minWidth = '30%';
            logContainer.style.height = '100vh';
            logContainer.style.margin = '0';
            logContainer.style.overflow = 'auto';
        }
        
        // Mostra a mensagem de instrução
        const instruction = document.getElementById('fullscreen-instruction');
        if (instruction) {
            instruction.style.display = 'block';
        }
        
        // Força redefinição do layout
        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
        }, 50);
    }
}

export function toggleBrowserFullscreen() {
    // console.log('toggleBrowserFullscreen chamado');
    // console.log('Estado atual de isFullScreenMode:', state.isFullScreenMode);
    
    if (!state.isFullScreenMode) {
        // console.log('Chamando enterBrowserFullscreen()');
        enterBrowserFullscreen();
    } else {
        // console.log('Chamando exitBrowserFullscreen()');
        exitBrowserFullscreen();
    }
}

export function enterBrowserFullscreen() {
    // console.log('Iniciando enterBrowserFullscreen()');
    
    // Guarda o estado original do sidebar
    state.originalSidebarCollapsed = document.body.classList.contains('sidebar-collapse');
    
    // console.log('Sidebar estava colapsado:', state.originalSidebarCollapsed);
    
    // Se o sidebar estiver colapsado, expande-o
    if (state.originalSidebarCollapsed) {
        // Remove a classe para expandir o sidebar
        document.body.classList.remove('sidebar-collapse');
        // console.log('Sidebar expandido para entrar em modo fullscreen');
    }
    
    const docElm = document.documentElement;
    
    try {
        if (docElm.requestFullscreen) {
            // console.log('Tentando requestFullscreen padrão');
            docElm.requestFullscreen();
        } else if (docElm.mozRequestFullScreen) {
            // console.log('Tentando mozRequestFullScreen');
            docElm.mozRequestFullScreen();
        } else if (docElm.webkitRequestFullscreen) {
            // console.log('Tentando webkitRequestFullscreen');
            docElm.webkitRequestFullscreen();
        } else if (docElm.msRequestFullscreen) {
            // console.log('Tentando msRequestFullscreen');
            docElm.msRequestFullscreen();
        } else {
            console.error('Nenhum método de fullscreen suportado');
        }
    } catch (error) {
        console.error('Erro ao tentar entrar em fullscreen:', error);
    }
    
    document.body.classList.add('browser-fullscreen');
    state.isFullScreenMode = true;
    
    // Atualiza o botão de controle
    const fullscreenBtn = document.getElementById('browserFullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i> Sair da Tela Cheia';
    }
    
    // Ajusta layout para aproveitar a tela inteira
    adjustFullscreenLayout();
    
    // Checa se há algum quadrante em modo fullscreen
    const fullscreenQuadrant = document.querySelector('.stream-container.fullscreen');
    if (fullscreenQuadrant) {
        fullscreenQuadrant.style.width = '100vw';
        fullscreenQuadrant.style.height = '100vh';
    }
}

export function exitBrowserFullscreen() {
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
        // console.log('Sidebar colapsado para retornar ao estado original');
    }
    
    // Atualiza o botão de controle
    const fullscreenBtn = document.getElementById('browserFullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.innerHTML = '<i class="fas fa-desktop"></i> Tela Cheia (F11)';
    }
    
    // Restaura o layout original
    restoreNormalLayout();
    
    // Restaura tamanho normal de quadrante em fullscreen
    const fullscreenQuadrant = document.querySelector('.stream-container.fullscreen');
    if (fullscreenQuadrant) {
        fullscreenQuadrant.style.width = '100%';
        fullscreenQuadrant.style.height = '100%';
    }
}

export function adjustFullscreenLayout() {
    const gridContainer = document.querySelector('.monitor-grid-container');
    
    if (!gridContainer) return;
    
    gridContainer.style.height = '100vh';
    gridContainer.style.width = '100vw';
    gridContainer.style.margin = '0';
    gridContainer.style.padding = '10px';
    
    // Maximiza todos os quadrantes para aproveitar o espaço da tela
    const allQuadrants = document.querySelectorAll('.stream-container:not(.fullscreen)');
    allQuadrants.forEach(quadrant => {
        quadrant.style.maxWidth = '100%';
        quadrant.style.maxHeight = '100%';
    });
    
    // Força esconder elementos do AdminLTE que podem atrapalhar em tela cheia
    const sidebar = document.querySelector('.main-sidebar');
    if (sidebar) {
        sidebar.style.width = '0';
        sidebar.style.display = 'none';
        sidebar.style.left = '-280px';
    }
    
    // Ajusta content-wrapper para ocupar toda a tela
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        contentWrapper.style.marginLeft = '0';
        contentWrapper.style.width = '100%';
    }
}

export function restoreNormalLayout() {
    const gridContainer = document.querySelector('.monitor-grid-container');
    
    if (!gridContainer) return;
    
    gridContainer.style.height = 'calc(100vh - 100px)';
    gridContainer.style.width = '';
    gridContainer.style.margin = '';
    gridContainer.style.padding = '';
    
    // Restaura o tamanho normal dos quadrantes
    const allQuadrants = document.querySelectorAll('.stream-container:not(.fullscreen)');
    allQuadrants.forEach(quadrant => {
        quadrant.style.maxWidth = '';
        quadrant.style.maxHeight = '';
    });
    
    // Restaura elementos do AdminLTE para o estado apropriado
    const sidebar = document.querySelector('.main-sidebar');
    if (sidebar) {
        sidebar.style.width = '';
        sidebar.style.display = '';
        sidebar.style.left = '';
    }
    
    // Restaura content-wrapper
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        contentWrapper.style.marginLeft = '';
        contentWrapper.style.width = '';
    }
}

export function handleFullscreenChange() {
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
                    // console.log('Sidebar expandido devido a mudança para fullscreen');
                }
            }
            
            document.body.classList.add('browser-fullscreen');
            state.isFullScreenMode = true;
            adjustFullscreenLayout();
        } else {
            document.body.classList.remove('browser-fullscreen');
            state.isFullScreenMode = false;
            
            // Restaura o estado original do sidebar ao sair do fullscreen
            if (state.originalSidebarCollapsed) {
                document.body.classList.add('sidebar-collapse');
                // console.log('Sidebar colapsado para retornar ao estado original');
            }
            
            restoreNormalLayout();
        }
        
        // Atualiza botão
        const fullscreenBtn = document.getElementById('browserFullscreenBtn');
        if (fullscreenBtn) {
            fullscreenBtn.innerHTML = isFullscreen ? 
                '<i class="fas fa-compress"></i> Sair da Tela Cheia' : 
                '<i class="fas fa-desktop"></i> Tela Cheia (F11)';
        }
    }
}