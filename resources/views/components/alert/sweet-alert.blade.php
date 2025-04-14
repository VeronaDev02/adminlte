@push('css')
<style>
    .custom-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 280px;
        max-width: 350px;
        background-color: white;
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        border-left: 4px solid;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease-in-out;
        overflow: hidden;
    }

    .custom-notification.show {
        opacity: 1;
        transform: translateX(0);
    }

    .custom-notification.success {
        border-left-color: #28a745;
    }

    .custom-notification.error {
        border-left-color: #dc3545;
    }

    .custom-notification-content {
        display: flex;
        padding: 12px 15px;
    }

    .custom-notification-icon {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        margin-right: 12px;
    }

    .custom-notification-icon.success {
        color: #28a745;
    }

    .custom-notification-icon.error {
        color: #dc3545;
    }

    .custom-notification-text {
        flex-grow: 1;
    }

    .custom-notification-title {
        font-weight: bold;
        font-size: 16px;
        margin: 0 0 5px 0;
    }

    .custom-notification-message {
        margin: 0;
        font-size: 14px;
        color: #666;
    }

    .custom-notification-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .custom-notification-progress::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform linear;
        background-color: rgba(0, 0, 0, 0.2);
    }

    .custom-notification.success .custom-notification-progress::after {
        background-color: rgba(40, 167, 69, 0.4);
    }

    .custom-notification.error .custom-notification-progress::after {
        background-color: rgba(220, 53, 69, 0.4);
    }

    .custom-notification.show .custom-notification-progress::after {
        transform: scaleX(1);
    }
</style>
@endpush

@push('js')
<script>
    // Função para mostrar notificação personalizada
    function mostrarNotificacao(mensagem, tipo = 'error', titulo = null, duracao = 3000) {
        const id = 'notification-' + Date.now();
        const icon = tipo === 'error' ? 
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg>' : 
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>';
        
        const notificationTitle = titulo || (tipo === 'error' ? 'Erro' : 'Sucesso');

        // Criar o elemento de notificação
        const notification = document.createElement('div');
        notification.id = id;
        notification.className = `custom-notification ${tipo === 'error' ? 'error' : 'success'}`;
        notification.innerHTML = `
            <div class="custom-notification-content">
                <div class="custom-notification-icon ${tipo === 'error' ? 'error' : 'success'}">
                    ${icon}
                </div>
                <div class="custom-notification-text">
                    <h4 class="custom-notification-title">${notificationTitle}</h4>
                    <p class="custom-notification-message">${mensagem}</p>
                </div>
            </div>
            <div class="custom-notification-progress"></div>
        `;

        // Adicionar ao DOM
        document.body.appendChild(notification);

        // Configurar a barra de progresso
        const progressBar = notification.querySelector('.custom-notification-progress::after');
        if (progressBar) {
            progressBar.style.transitionDuration = duracao + 'ms';
        }

        // Mostrar a notificação
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Configurar o fechamento automático
        const timer = setTimeout(() => {
            closeNotification(notification);
        }, duracao);

        // Pausar o timer ao passar o mouse sobre a notificação
        notification.addEventListener('mouseenter', () => {
            clearTimeout(timer);
            if (progressBar) {
                progressBar.style.transitionProperty = 'none';
            }
        });

        // Retomar o timer ao retirar o mouse
        notification.addEventListener('mouseleave', () => {
            const newTimer = setTimeout(() => {
                closeNotification(notification);
            }, duracao / 2);
            if (progressBar) {
                progressBar.style.transitionProperty = 'transform';
            }
        });

        // Função para fechar a notificação
        function closeNotification(element) {
            element.classList.remove('show');
            setTimeout(() => {
                element.remove();
            }, 300);
        }

        // Permitir que o usuário feche a notificação clicando nela
        notification.addEventListener('click', () => {
            closeNotification(notification);
        });

        return notification;
    }

    // Alias para mostrar alerta de sucesso com ícone de check verde
    function mostrarSucesso(mensagem, titulo = 'Sucesso', duracao = 3000) {
        return mostrarNotificacao(mensagem, 'success', titulo, duracao);
    }

    // Alias para mostrar alerta de erro com ícone X vermelho
    function mostrarErro(mensagem, titulo = 'Erro', duracao = 3000) {
        return mostrarNotificacao(mensagem, 'error', titulo, duracao);
    }

    function confirmarAcao(mensagem, callback) {
        Swal.fire({
            title: 'Confirmação',
            text: mensagem,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }
</script>
@endpush