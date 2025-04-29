$(function () {
    $(document).on('click', '#theme-switch', function (e) {
        e.preventDefault();
        
        const isDarkMode = $('body').hasClass('dark-mode');
        const newTheme = isDarkMode ? 'light' : 'dark';
        
        if (newTheme === 'dark') {
            $('body').addClass('dark-mode');
            $('.navbar').removeClass('navbar-white navbar-light').addClass('navbar-dark');
        } else {
            $('body').removeClass('dark-mode');
            $('.navbar').removeClass('navbar-dark').addClass('navbar-white navbar-light');
        }
        
        saveUserPreferences({
            theme: newTheme,
            sidebar_collapsed: $('body').hasClass('sidebar-collapse')
        });
    });
    
    $(document).on('click', '[data-widget="pushmenu"]', function() {
        setTimeout(function() {
            const isCollapsed = $('body').hasClass('sidebar-collapse');
            // console.log('Menu estado após clique:', isCollapsed ? 'Colapsado' : 'Expandido');
            
            saveUserPreferences({
                theme: $('body').hasClass('dark-mode') ? 'dark' : 'light',
                sidebar_collapsed: isCollapsed
            });
        }, 200);
    });
    
    function saveUserPreferences(preferences) {
        // console.log('Salvando preferências:', preferences);
        
        $.ajax({
            url: '/save-ui-preferences',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                preferences: preferences
            },
            success: function(response) {
                // console.log('Preferências salvas com sucesso:', response);
            },
            error: function(error) {
                console.error('Erro ao salvar preferências:', error);
            }
        });
    }
    
    function loadUserPreferences() {
        try {
            const userPreferences = JSON.parse($('meta[name="user-preferences"]').attr('content') || '{}');
            // console.log('Carregando preferências:', userPreferences);
            
            if (userPreferences.theme === 'dark') {
                $('body').addClass('dark-mode');
                $('.navbar').removeClass('navbar-white navbar-light').addClass('navbar-dark');
            } else if (userPreferences.theme === 'light') {
                // Explicitamente tratar o tema light para garantir consistência
                $('body').removeClass('dark-mode');
                $('.navbar').removeClass('navbar-dark').addClass('navbar-white navbar-light');
            }
            
            const sidebarCollapsed = userPreferences.sidebar_collapsed === true || 
                                    userPreferences.sidebar_collapsed === "true";
            
            // console.log('Estado do menu a ser aplicado:', sidebarCollapsed ? 'Colapsado' : 'Expandido');
            
            if (sidebarCollapsed) {
                if (!$('body').hasClass('sidebar-collapse')) {
                    $('body').addClass('sidebar-collapse');
                    // console.log('Adicionando classe sidebar-collapse');
                }
            } else {
                if ($('body').hasClass('sidebar-collapse')) {
                    $('body').removeClass('sidebar-collapse');
                    // console.log('Removendo classe sidebar-collapse');
                }
            }
            
            // console.log('Estado final do menu:', $('body').hasClass('sidebar-collapse') ? 'Colapsado' : 'Expandido');
        } catch (e) {
            console.error('Erro ao carregar preferências:', e);
        }
    }
    
    $(document).ready(function() {
        loadUserPreferences();
        
        setTimeout(function() {
            loadUserPreferences();
        }, 300);
    });
});