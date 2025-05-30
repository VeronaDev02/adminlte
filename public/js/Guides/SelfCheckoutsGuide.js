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
    title: 'SelfCheckouts',
    text: 'Nessa página iremos criar suas telas para monitorar.',
    buttons: [
        {
            action() { return this.next(); },
            text: 'Começar tutorial'
        }
    ],
    id: 'creating'
});

tour.addStep({
    title: 'Quantidade de Telas',
    text: 'Clique e selecione a quantidade de telas que deseja criar, ou seja, quantos PDVs quer observar.',
    attachTo: { element: '.first', on: 'bottom' },
    buttons: [
        { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
        {
            action() {
                const selectedQuadrants = document.querySelector('.first').value;
                if (selectedQuadrants && selectedQuadrants !== '0') {
                    return this.next();
                } else {
                    return tour.show('validation-error-quadrants');
                }
            },
            text: 'Próximo'
        }
    ],
    id: 'select-quadrants'
});

tour.addStep({
    title: 'Número de colunas',
    text: 'Escolha quantas colunas deseja. Ou seja, quantos PDVs vão estar um ao lado do outro em sua tela.',
    attachTo: { element: 'select[wire\\:model="selectedColumns"]', on: 'right' },
    buttons: [
        { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
        {
            action() {
                const selectedColumns = document.querySelector('select[wire\\:model="selectedColumns"]').value;
                if (selectedColumns && selectedColumns !== '0') {
                    setTimeout(() => {
                        const previewElement = document.querySelector('.preview-container');
                        if (previewElement && previewElement.offsetHeight > 0 && previewElement.offsetWidth > 0) {
                            return this.next();
                        } else {
                            tour.show('map-pdvs-step');
                        }
                    }, 100);
                } else {
                    return tour.show('validation-error-columns');
                }
            },
            text: 'Próximo'
        }
    ],
    id: 'select-columns'
});

tour.addStep({
    title: 'Preview da Tela',
    text: 'Aqui é uma demonstração de como ficará sua tela com as configurações escolhidas.',
    attachTo: { element: '.preview-container', on: 'top' },
    buttons: [
        { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
        { action() { return this.next(); }, text: 'Próximo' }
    ],
    id: 'preview-step'
});

tour.addStep({
    title: 'Mapeamento de PDVs',
    text: 'Agora, mapeie seus PDVs para cada tela. Cada caixa representa uma tela na sua visualização final.',
    attachTo: { element: '.screen-grid', on: 'top' },
    buttons: [
        { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
        { action() { return this.next(); }, text: 'Próximo' }
    ],
    id: 'map-pdvs-step'
});

tour.addStep({
    title: 'Confirmar',
    text: 'Caso desejar, você pode confirmar as telas que criou e começar a monitorar seus PDVs.',
    attachTo: { element: '.btn.btn-primary[wire\\:click="applyConfiguration"]', on: 'top' },
    buttons: [
        { action() { return this.back(); }, classes: 'shepherd-button-secondary', text: 'Anterior' },
        { action() { return tour.complete(); }, text: 'Finalizar Tour' }
    ],
    id: 'confirm-creation'
});

if(document.querySelector('.fifth')) {
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

tour.addStep({
    title: 'Campo Requerido!',
    text: 'Ops! Por favor, selecione um valor para a quantidade de telas antes de continuar.',
    buttons: [
        {
            action() {
                return tour.show('select-quadrants');
            },
            classes: 'shepherd-button-secondary',
            text: 'Voltar e Corrigir'
        },
        {
            action() {
                return tour.complete();
            },
            text: 'Fechar Tour'
        }
    ],
    id: 'validation-error-quadrants',
    cancelIcon: { enabled: true },
    useModalOverlay: true
});

tour.addStep({
    title: 'Campo Requerido!',
    text: 'Ops! Por favor, selecione um valor para o número de colunas antes de continuar.',
    buttons: [
        {
            action() {
                return tour.show('select-columns');
            },
            classes: 'shepherd-button-secondary',
            text: 'Voltar e Corrigir'
        },
        {
            action() {
                return tour.complete();
            },
            text: 'Fechar Tour'
        }
    ],
    id: 'validation-error-columns',
    cancelIcon: { enabled: true },
    useModalOverlay: true
});

function startTour(){
    tour.start();
}