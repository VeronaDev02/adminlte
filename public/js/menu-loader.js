$(function () {
    // Obtém o token CSRF do meta tag no cabeçalho da página
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Adiciona o token CSRF em todas as requisições AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // Adiciona o CSS para o botão de excluir
    const buttonCss = `
        <style>
            .delete-tela {
                margin-left: 10px; /* Espaçamento à esquerda */
                float: right; /* Faz o botão flutuar à direita */
                display: inline-block; /* Alinha o botão ao lado */
            }
        </style>
    `;
    $("head").append(buttonCss); // Adiciona o CSS ao cabeçalho

    // Função para carregar o menu dinâmico
    function loadDynamicMenu() {
        console.log('Iniciando carregamento do menu dinâmico');

        $.ajax({
            url: '/menu',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Resposta do servidor:', response);

                if (response.menu && Array.isArray(response.menu)) {
                    console.log('Menu válido recebido');

                    // Seleciona o sidebar de forma mais abrangente
                    const $sidebar = $('.sidebar .nav-sidebar');
                    
                    // Remove itens dinâmicos anteriores
                    $sidebar.find('.dynamic-menu-item').remove();

                    // Renderiza os novos itens de menu
                    response.menu.forEach(function(menuItem) {
                        console.log('Processando item de menu:', menuItem);

                        if (menuItem.submenu) {
                            console.log('Criando menu com submenu');
                            const $parentLi = createMenuWithSubmenu(menuItem);
                            $sidebar.append($parentLi);
                        } else {
                            console.log('Criando item de menu simples');
                            const $menuItemElement = createSimpleMenuItem(menuItem);
                            $sidebar.append($menuItemElement);
                        }
                    });

                    // Tenta reinicializar o menu do AdminLTE
                    try {
                        if ($.AdminLTE && typeof $.AdminLTE.layout === 'object') {
                            $.AdminLTE.layout.fixSidebar();
                        }
                    } catch (error) {
                        console.error('Erro ao reinicializar sidebar:', error);
                    }
                } else {
                    console.warn('Resposta do menu inválida');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar menu dinâmico:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
            }
        });
    }

    // Função para criar item de menu simples
    function createSimpleMenuItem(item) {
        console.log('Criando item de menu simples:', item);

        const $li = $('<li>').addClass('nav-item dynamic-menu-item');
        
        if (item.admin) {
            $li.addClass('admin-menu-item');
        }

        const $a = $('<a>') 
            .addClass('nav-link')
            .attr('href', item.url || '#')
            .appendTo($li);

        $('<i>')
            .addClass(item.icon || 'fas fa-circle')
            .addClass('nav-icon')
            .prependTo($a);

        $('<p>').text(item.text).appendTo($a);

        // Adiciona o botão de excluir (se a URL de exclusão estiver presente)
        if (item.delete_url) {
            const $deleteButton = $('<button>')  // Cria o botão de excluir
                .addClass('btn btn-danger btn-sm delete-tela')
                .text('Excluir')
                .attr('data-url', item.delete_url)
                .appendTo($a);  // Coloca o botão dentro do link (ao lado do texto)
        }

        return $li;
    }

    // Função para criar menu com submenu
    function createMenuWithSubmenu(item) {
        console.log('Criando menu com submenu:', item);

        const $li = $('<li>').addClass('nav-item dynamic-menu-item has-treeview');
        
        if (item.admin) {
            $li.addClass('admin-menu-item');
        }

        const $a = $('<a>')
            .addClass('nav-link')
            .attr('href', '#')
            .appendTo($li);

        $('<i>')
            .addClass(item.icon || 'fas fa-folder')
            .addClass('nav-icon')
            .prependTo($a);

        $('<p>')
            .text(item.text)
            .append($('<i>').addClass('fas fa-angle-left right'))
            .appendTo($a);

        // Cria submenu
        const $subMenu = $('<ul>').addClass('nav nav-treeview');
        
        item.submenu.forEach(function(subItem) {
            const $subLi = createSimpleMenuItem(subItem);
            $subMenu.append($subLi);
        });

        $li.append($subMenu);

        return $li;
    }

    // Função para excluir a tela
    function deleteTela(url) {
        console.log('Excluindo tela com URL:', url);

        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(response) {
                console.log('Tela excluída com sucesso:', response);
                loadDynamicMenu(); // Recarrega o menu após a exclusão
            },
            error: function(xhr, status, error) {
                console.error('Erro ao excluir tela:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
            }
        });
    }

    // Ao clicar no botão de excluir
    $(document).on('click', '.delete-tela', function(e) {
        e.preventDefault(); // Evita o comportamento padrão do botão (caso tenha algum link)
        
        const deleteUrl = $(this).data('url'); // Pega a URL de exclusão associada ao botão
        deleteTela(deleteUrl); // Chama a função de exclusão
    });

    // Carrega o menu dinâmico quando o documento estiver pronto
    $(document).ready(function() {
        console.log('Documento pronto. Carregando menu dinâmico.');
        loadDynamicMenu();
    });

    // Opcional: Recarregar menu se necessário
    $(document).on('user:login', function() {
        console.log('Evento user:login disparado');
        loadDynamicMenu();
    });
});
