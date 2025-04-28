@extends('adminlte::page')

@section('title', 'SelfCheckouts')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>SelfCheckouts</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-tv mr-2"></i>Configurações de Tela
                <button class="btn btn-tool toggle-config" type="button" data-toggle="collapse" data-target="#quadrant-config">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </h3>
        </div>
        
        <div id="quadrant-config" class="card-body collapse show">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Quantidade de Telas</label>
                        <select id="quadrant-select" class="form-control">
                            <option value="">Selecione a quantidade de telas</option>
                            @foreach($grid->getQuadrantOptions() as $option)
                                <option value="{{ $option }}">{{ $option }} Telas</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Número de Colunas</label>
                        <select id="columns-select" class="form-control" disabled>
                            <option value="">Selecione primeiro a quantidade de telas</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Número de Linhas</label>
                        <input type="text" id="rows-display" class="form-control" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <div class="layout-preview" id="layout-preview">
                    </div>
                </div>
            </div>
            
            <div id="screen-mapping-container" style="display: none;">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Seleção de PDVs por Tela</h3>
                            </div>
                            <div class="card-body" id="screen-mapping">
                                <div class="screen-selector-container">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button id="apply-config" class="btn btn-primary" disabled>
                    <i class="fas fa-check mr-2"></i>Aplicar Configuração
                </button>
            </div>
        </div>
    </div>

    <div id="grid-container" style="display: none;">
        <div class="card">
            <div class="card-body p-1">
                <div id="grid-pdv-names" class="mb-3 p-2 d-flex justify-content-between align-items-center">
                    <div class="pdv-badges">
                    </div>
                    <button id="fullscreen-btn" class="btn btn-secondary btn-sm">
                        <i class="fas fa-expand"></i> Tela Cheia
                    </button>
                </div>
                <div id="dynamic-grid-container">
                    @if(request()->has('quadrants') && request()->has('cols') && request()->has('rows'))
                        <x-selfs-grid 
                            :pdvDataList="$pdvDataList" 
                            :serverConfig="$serverConfig"
                            :activeQuadrants="request()->input('quadrants')"
                            :cols="request()->input('cols')"
                            :rows="request()->input('rows')"
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quadrantSelect = document.getElementById('quadrant-select');
    const columnsSelect = document.getElementById('columns-select');
    const rowsDisplay = document.getElementById('rows-display');
    const screenSelectorContainer = document.querySelector('.screen-selector-container');
    const applyConfigBtn = document.getElementById('apply-config');
    const screenMappingContainer = document.getElementById('screen-mapping-container');
    const gridContainer = document.getElementById('grid-container');
    const gridPdvNames = document.getElementById('grid-pdv-names');
    const layoutPreview = document.getElementById('layout-preview');
    const dynamicGridContainer = document.getElementById('dynamic-grid-container');

    const fullscreenBtn = document.getElementById('fullscreen-btn');
    let isFullscreen = false;
    let originalParent = null;
    let originalStyles = {};

    fullscreenBtn.addEventListener('click', function() {
        if (!isFullscreen) {
            enterFullscreen();
        } else {
            exitFullscreen();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isFullscreen) {
            exitFullscreen();
        }
    });

    function enterFullscreen() {
        originalParent = dynamicGridContainer.parentElement;
        originalStyles = {
            position: dynamicGridContainer.style.position,
            top: dynamicGridContainer.style.top,
            left: dynamicGridContainer.style.left,
            width: dynamicGridContainer.style.width,
            height: dynamicGridContainer.style.height,
            zIndex: dynamicGridContainer.style.zIndex,
            backgroundColor: dynamicGridContainer.style.backgroundColor
        };
        
        const fullscreenOverlay = document.createElement('div');
        fullscreenOverlay.id = 'fullscreen-overlay';
        fullscreenOverlay.style.position = 'fixed';
        fullscreenOverlay.style.top = '0';
        fullscreenOverlay.style.left = '0';
        fullscreenOverlay.style.width = '100vw';
        fullscreenOverlay.style.height = '100vh';
        fullscreenOverlay.style.backgroundColor = '#000';
        fullscreenOverlay.style.zIndex = '9999';
        fullscreenOverlay.style.display = 'flex';
        fullscreenOverlay.style.flexDirection = 'column';
        fullscreenOverlay.style.padding = '10px';
        
        const exitButton = document.createElement('button');
        exitButton.className = 'btn btn-secondary btn-sm align-self-end mb-2';
        exitButton.innerHTML = '<i class="fas fa-compress"></i> Sair da Tela Cheia';
        exitButton.addEventListener('click', exitFullscreen);
        
        const fullscreenContainer = document.createElement('div');
        fullscreenContainer.id = 'fullscreen-container';
        fullscreenContainer.style.flex = '1';
        fullscreenContainer.style.overflow = 'hidden';
        fullscreenContainer.style.width = '100%';
        fullscreenContainer.style.height = '100%';
        fullscreenContainer.style.display = 'flex';
        
        fullscreenOverlay.appendChild(exitButton);
        fullscreenOverlay.appendChild(fullscreenContainer);
        
        document.body.appendChild(fullscreenOverlay);
        
        fullscreenContainer.appendChild(dynamicGridContainer);
        dynamicGridContainer.style.width = '100%';
        dynamicGridContainer.style.height = '100%';
        
        const mainGrid = document.getElementById('mainGrid');
        if (mainGrid) {
            mainGrid.style.height = '100%';
            mainGrid.style.width = '100%';
            mainGrid.style.minHeight = '100%';
            mainGrid.style.gap = '2px';
            
            // Corrigindo display de todas as células no grid
            const streamContainers = mainGrid.querySelectorAll('.stream-container');
            streamContainers.forEach(container => {
                container.style.display = 'flex'; // Garantir que todas as células estejam visíveis
            });
        }
        
        fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i> Sair da Tela Cheia';
        
        isFullscreen = true;
        
        document.body.style.overflow = 'hidden';
        
        requestFullscreen(document.documentElement);
        
        recalculateStreamContainers();
    }

    function exitFullscreen() {
        const overlay = document.getElementById('fullscreen-overlay');
        if (overlay) {
            originalParent.appendChild(dynamicGridContainer);
            
            Object.keys(originalStyles).forEach(key => {
                dynamicGridContainer.style[key] = originalStyles[key];
            });
            
            const mainGrid = document.getElementById('mainGrid');
            if (mainGrid) {
                mainGrid.style.height = '';
                mainGrid.style.minHeight = 'calc(100vh - 250px)';
                mainGrid.style.width = '';
                mainGrid.style.gap = '1px';
            }
            
            overlay.remove();
        }
        
        fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i> Tela Cheia';
        
        isFullscreen = false;
        
        document.body.style.overflow = 'auto';
        
        exitBrowserFullscreen();
        
        recalculateStreamContainers();
    }

    function requestFullscreen(element) {
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) { 
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) { 
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    }

    function exitBrowserFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { 
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    function recalculateStreamContainers() {
        setTimeout(() => {
            const mainGrid = document.getElementById('mainGrid');
            if (!mainGrid) return;
            
            const streamContainers = mainGrid.querySelectorAll('.stream-container');
            const totalCells = selectedScreenCount || streamContainers.length;
            
            if (isFullscreen) {
                mainGrid.style.height = '100%';
                mainGrid.style.minHeight = '100%';
                mainGrid.style.width = '100%';
                mainGrid.style.gap = '2px';
                
                // Ajustar o grid para mostrar todas as células
                // Verificar e ajustar o grid-template-columns e grid-template-rows se necessário
                if (selectedColumns && selectedRows) {
                    mainGrid.style.gridTemplateColumns = `repeat(${selectedColumns}, 1fr)`;
                    mainGrid.style.gridTemplateRows = `repeat(${selectedRows}, 1fr)`;
                }
            }
            
            streamContainers.forEach((container, index) => {
                // Garantir que todas as células até o número de telas selecionadas estejam visíveis
                if (index < totalCells) {
                    container.style.display = 'flex';
                }
                
                container.style.height = '';
                container.style.width = '';
                container.style.maxHeight = '';
                
                if (isFullscreen) {
                    container.style.height = '100%';
                    container.style.maxHeight = 'none';
                    
                } else {
                    const aspectRatio = 16 / 9;
                    const containerWidth = container.clientWidth;
                    const calculatedHeight = containerWidth / aspectRatio;
                    
                    container.style.height = `${calculatedHeight}px`;
                    container.style.maxHeight = '100%';
                }
                
                const logContainer = container.querySelector('.log-container');
                const videoContainer = container.querySelector('.video-container');
                
                if (logContainer && videoContainer) {
                    logContainer.style.height = '100%';
                    videoContainer.style.height = '100%';
                }
            });
            
            const videos = mainGrid.querySelectorAll('video');
            videos.forEach(video => {
                if (video.videoWidth && video.videoHeight) {
                    video.style.width = '100%';
                    video.style.height = '100%';
                    video.style.objectFit = 'contain';
                }
            });
        }, 100);
    }

    const pdvs = @json($pdvDataList);
    let selectedScreenCount = 0;
    let selectedColumns = 0;
    let selectedRows = 0;

    function updateColumnOptions() {
        columnsSelect.innerHTML = '';
        columnsSelect.disabled = true;
        
        if (!selectedScreenCount) {
            columnsSelect.innerHTML = '<option value="">Selecione primeiro a quantidade de telas</option>';
            return;
        }
        
        const defaultOption = document.createElement('option');
        defaultOption.textContent = 'Selecione o número de colunas';
        defaultOption.value = '';
        columnsSelect.appendChild(defaultOption);
        
        for (let i = 1; i <= selectedScreenCount; i++) {
            if (selectedScreenCount % i === 0) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `${i} ${i === 1 ? 'Coluna' : 'Colunas'}`;
                columnsSelect.appendChild(option);
            }
        }
        
        columnsSelect.disabled = false;
    }

    function updateRowsDisplay() {
        if (!selectedScreenCount || !selectedColumns) {
            rowsDisplay.value = '';
            return;
        }
        
        selectedRows = selectedScreenCount / selectedColumns;
        
        // Only proceed if we have a valid whole number of rows
        if (selectedRows % 1 !== 0) {
            rowsDisplay.value = '';
            return;
        }
        
        rowsDisplay.value = `${selectedRows} ${selectedRows === 1 ? 'Linha' : 'Linhas'}`;
        
        updateLayoutPreview();
    }

    function updateLayoutPreview() {
        if (!selectedColumns || !selectedRows) {
            layoutPreview.innerHTML = '';
            return;
        }
        
        let previewHtml = `
            <label style="margin-bottom: 8px; display: block; text-align: center; color: #ecf0f1;">Layout</label>
            <div class="layout-grid" style="
            display: grid;
            grid-template-columns: repeat(${selectedColumns}, 1fr);
            grid-template-rows: repeat(${selectedRows}, 1fr);
            gap: 5px;
            background-color: #343a40;
            padding: 10px;
            border-radius: 5px;
            height: 200px;
        ">`;
        
        for (let i = 0; i < selectedScreenCount; i++) {
            previewHtml += `<div class="layout-cell" style="
                background-color: #5a636a;
                border-radius: 3px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
            ">${i + 1}</div>`;
        }
        
        previewHtml += '</div>';
        layoutPreview.innerHTML = previewHtml;
    }

    function updateUI() {
        updateColumnOptions();
        
        updateRowsDisplay();
        
        // Only show screen mapping if we have valid values for all three parameters
        // and if rows is a whole number (not a decimal)
        if (selectedScreenCount && selectedColumns && selectedRows && selectedRows % 1 === 0) {
            screenMappingContainer.style.display = 'block';
            applyConfigBtn.disabled = false;
            
            updateScreenMapping();
        } else {
            screenMappingContainer.style.display = 'none';
            applyConfigBtn.disabled = true;
        }
    }
    
    function updateGridDisplay() {
        // Esta função assegura que o grid está exibindo todas as células corretamente
        const mainGrid = document.getElementById('mainGrid');
        if (!mainGrid) return;
        
        // Garantir que as propriedades do grid estão definidas corretamente
        if (selectedColumns && selectedRows) {
            mainGrid.style.gridTemplateColumns = `repeat(${selectedColumns}, 1fr)`;
            mainGrid.style.gridTemplateRows = `repeat(${selectedRows}, 1fr)`;
        }
        
        // Garantir que todas as células necessárias estão visíveis
        const streamContainers = mainGrid.querySelectorAll('.stream-container');
        streamContainers.forEach((container, index) => {
            if (index < selectedScreenCount) {
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
            }
        });
    }

    function updateScreenMapping() {
        screenSelectorContainer.innerHTML = '';
        
        // Use grid layout matching the layout preview's configuration
        const gridContainer = document.createElement('div');
        gridContainer.classList.add('screen-grid');
        gridContainer.style.display = 'grid';
        gridContainer.style.gridTemplateColumns = `repeat(${selectedColumns}, 1fr)`;
        gridContainer.style.gap = '15px';
        gridContainer.style.padding = '15px';
        
        for (let i = 1; i <= selectedScreenCount; i++) {
            const screenDiv = document.createElement('div');
            screenDiv.classList.add('screen-item');
            
            const label = document.createElement('label');
            label.textContent = `Tela ${i}`;
            label.style.color = '#333'; // Ensuring visibility in dark mode
            label.style.display = 'block';
            label.style.marginBottom = '5px';
            label.style.fontWeight = 'bold';
            
            const select = document.createElement('select');
            select.classList.add('form-control');
            select.setAttribute('data-screen', i);

            const defaultOption = document.createElement('option');
            defaultOption.textContent = 'Selecione um PDV';
            defaultOption.value = '';
            select.appendChild(defaultOption);

            pdvs.forEach(pdv => {
                const option = document.createElement('option');
                option.value = pdv.id;
                option.textContent = `${pdv.nome} (${pdv.pdvIp})`;
                select.appendChild(option);
            });

            screenDiv.appendChild(label);
            screenDiv.appendChild(select);
            gridContainer.appendChild(screenDiv);
        }
        
        screenSelectorContainer.appendChild(gridContainer);
    }

    quadrantSelect.addEventListener('change', function() {
        selectedScreenCount = parseInt(this.value) || 0;
        // Reset columns and rows when changing screen count
        selectedColumns = 0;
        selectedRows = 0;
        columnsSelect.value = '';
        rowsDisplay.value = '';
        updateUI();
    });

    columnsSelect.addEventListener('change', function() {
        selectedColumns = parseInt(this.value) || 0;
        updateUI();
    });

    applyConfigBtn.addEventListener('click', function() {
        const selectedPdvs = [];
        const screenSelects = document.querySelectorAll('.screen-item select');
        let allSelected = true;
        
        screenSelects.forEach((select, index) => {
            if (select.value) {
                const selectedPdv = pdvs.find(pdv => pdv.id == select.value);
                selectedPdvs.push(selectedPdv);
            } else {
                allSelected = false;
            }
        });

        if (!allSelected) {
            alert(`Por favor, selecione PDVs para todas as ${selectedScreenCount} telas.`);
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('quadrants', selectedScreenCount);
        url.searchParams.set('cols', selectedColumns);
        url.searchParams.set('rows', selectedRows);
        
        selectedPdvs.forEach((pdv, index) => {
            url.searchParams.set(`pdv[${index}]`, pdv.id);
        });
        
        window.location.href = url.toString();
    });

    updateUI();
    
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('quadrants') && urlParams.has('cols') && urlParams.has('rows')) {
        quadrantSelect.value = urlParams.get('quadrants');
        selectedScreenCount = parseInt(urlParams.get('quadrants'));
        
        updateColumnOptions();
        
        columnsSelect.value = urlParams.get('cols');
        selectedColumns = parseInt(urlParams.get('cols'));
        
        selectedRows = parseInt(urlParams.get('rows'));
        rowsDisplay.value = `${selectedRows} ${selectedRows === 1 ? 'Linha' : 'Linhas'}`;
        
        updateUI();
        
        const selectedPdvs = [];
        
        for (let i = 0; i < selectedScreenCount; i++) {
            const pdvId = urlParams.get(`pdv[${i}]`);
            if (pdvId) {
                const pdv = pdvs.find(p => p.id == pdvId);
                if (pdv) {
                    selectedPdvs.push(pdv);
                }
            }
        }
        
        if (selectedPdvs.length > 0) {
            const screenSelects = document.querySelectorAll('.screen-item select');
            for (let i = 0; i < Math.min(screenSelects.length, selectedPdvs.length); i++) {
                screenSelects[i].value = selectedPdvs[i].id;
            }
            
            gridContainer.style.display = 'block';
            
            const pdvBadges = gridPdvNames.querySelector('.pdv-badges');
            pdvBadges.innerHTML = '';
            selectedPdvs.forEach((pdv) => {
                const pdvNameElement = document.createElement('div');
                pdvNameElement.classList.add('badge', 'badge-info', 'mr-2');
                pdvNameElement.textContent = `${pdv.nome}`;
                pdvBadges.appendChild(pdvNameElement);
            });
            
            // Chamar a função para garantir que o grid está exibindo todas as células
            setTimeout(updateGridDisplay, 500);
        }
    }
    
    // Adicionar um event listener para quando a página terminar de carregar
    window.addEventListener('load', function() {
        if (document.getElementById('mainGrid')) {
            updateGridDisplay();
            recalculateStreamContainers();
        }
    });
});
</script>
@stop

@section('css')
<style>
    #fullscreen-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: #000;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        padding: 10px;
    }

    #fullscreen-container {
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
    }

    #fullscreen-container #dynamic-grid-container {
        width: 100% !important;
        height: 100% !important;
        flex: 1;
    }

    #fullscreen-container .stream-grid {
        width: 100% !important;
        height: 100% !important;
        min-height: 100% !important;
    }

    .pdv-badges {
        display: flex;
        flex-wrap: wrap;
    }

    #fullscreen-btn {
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    #fullscreen-btn i {
        font-size: 1rem;
    }

    #screen-mapping .screen-item {
        margin-bottom: 15px;
    }

    .screen-selector-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .screen-selector-container .screen-item {
        flex: 1;
        min-width: 200px;
    }

    .layout-preview {
        margin: 20px 0;
    }

    .layout-cell {
        min-height: 50px;
        border: 1px solid;
        background-color: #5a636a;
        color: #ffffff;
    }

    .layout-grid {
        display: grid;
        gap: 5px;
        padding: 10px;
        border-radius: 5px;
        height: 200px;
        border: 1px solid;
        background-color: #343a40;
    }
    
    /* Ajustes de responsividade para o grid */
    @media (max-width: 768px) {
        #fullscreen-overlay {
            padding: 5px;
        }
        
        .stream-container {
            flex-direction: column;
        }
        
        .log-container, .video-container {
            width: 100% !important;
            height: auto !important;
        }
        
        .log-container {
            height: 150px !important;
        }
        
        .video-container {
            flex: 1;
        }
    }
</style>
@stop