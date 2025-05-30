const tour = new Shepherd.Tour({
    useModalOverlay: true,
    defaultStepOptions: {
        cancelIcon: {
            enabled: true
        },
        scrollTo: { behavior: 'smooth', block: 'center' }
    }
});

tour.addStep({
    title: 'Bem-vindo a página inicial',
    text: 'Nessa tela você verá todas as suas opções.',
    buttons: [
        {
            action() { return this.next(); },
            text: 'Começar tutorial'
        }
    ],
    id: 'creating'
});

tour.addStep({
    title: 'Editar Perfil',
    text: 'Utilizado para alterar e exibir informações relacionadas ao seu perfil.',
    attachTo: { element: '.first', on: 'top' },
    buttons: [
        { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
        { action() { return this.next(); }, text: 'Próximo' }
    ],
    id: 'editing-profile'
});

tour.addStep({
    title: 'Alterar senha',
    text: 'Aqui você pode alterar diretamente sua senha de acesso ao sistema.',
    attachTo: { element: '.second', on: 'top' },
    buttons: [
        { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
        { action() { return this.next(); }, text: 'Próximo' }
    ],
    id: 'change-password'
});

// tour.addStep({
//     title: 'Perfil',
//     text: 'É aqui onde você pode encontrar e alterar as informações relacionadas ao seu perfil, como foto, nome, e-mail, telefone e data de criação.',
//     attachTo: { element: '.fourth', on: 'left' },
//     buttons: [
//         { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
//         { action() { return this.next(); }, text: 'Próximo' }
//     ],
//     id: 'profile'
// });

if(document.querySelector('.fifth')) {
    tour.addStep({
        title: 'Self Checkouts',
        text: 'Utilizado para criar uma configuração de tela, os PDVs que deseja ver e qual o jeito que quer exibi-los.',
        attachTo: { element: '.third', on: 'right' },
        buttons: [
            { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
            { action() { return this.next(); }, text: 'Próximo' }
        ],
        id: 'selfcheckouts'
    });
    tour.addStep({
        title: 'Monitoramento',
        text: 'É aqui que pode clicar em visualizar a tela do selfcheckout, onde você pode ver o que está acontecendo em tempo real.',
        attachTo: { element: '.fifth', on: 'left' },
        buttons: [
            { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
            { action() { return this.complete(); }, text: 'Finalizar' }
        ],
        id: 'monitoring'
    });
}
else {
    tour.addStep({
        title: 'Self Checkouts',
        text: 'Utilizado para criar uma configuração de tela, os PDVs que deseja ver e qual o jeito que quer exibi-los.',
        attachTo: { element: '.third', on: 'right' },
        buttons: [
            { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
            { action() { return this.complete(); }, text: 'Finalizar Tour' }
        ],
        id: 'selfcheckouts'
    });
}

function startTour(){
    // colocar parâmetro de id do usuário caso queira salvar no localStorage
    // if(localStorage.getItem(id+"dashboard_guide") == null){
    tour.start();
    // localStorage.setItem(id+"dashboard_guide", "true");
    // }
}