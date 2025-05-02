<div>
    <div class="monitor-header">
        <h1>{{ $pageTitle }}</h1>
        <div class="monitor-controls">
            <button id="browserFullscreenBtn" onclick="toggleBrowserFullscreen()">
                <i class="fas fa-desktop"></i> Tela Cheia (F11)
            </button>
            <div id="serverStatus" class="server-status">
                Conectando ao servidor...
            </div>
        </div>
    </div>
    
    <div class="monitor-grid-container" style="grid-template-columns: repeat({{ $columns }}, 1fr); grid-template-rows: repeat({{ $rows }}, 1fr);">
        @for($i = 1; $i <= $quadrants; $i++)
            @php 
                $pdvInfo = isset($pdvData[$i]) ? $pdvData[$i] : null;
                $pdvName = $pdvInfo ? $pdvInfo['nome'] : 'Sem PDV';
                $pdvIp = $pdvInfo ? $pdvInfo['pdvIp'] : '';
                $pdvCode = $pdvInfo ? $pdvInfo['pdvCodigo'] : '';
            @endphp
            <div id="quadrant{{ $i }}" class="stream-container">
                <div class="video-container">
                    <video id="remoteVideo{{ $i }}" autoplay muted playsinline></video>
                    <span id="status{{ $i }}" class="status-indicator">Desconectado</span>
                    <button class="fullscreen-btn" onclick="toggleQuadrantFullscreen(document.getElementById('quadrant{{ $i }}'))">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
                
                <div id="log{{ $i }}" class="log-container">
                    <div class="log-header">
                        <strong>{{ $pdvName }}</strong>
                        <small>{{ $pdvIp }} ({{ $pdvCode }})</small>
                    </div>
                    <div class="log-content"></div>
                </div>
            </div>
        @endfor
    </div>
    
    <!-- Mensagem de instrução de tela cheia (inicialmente escondida) -->
    <div id="fullscreen-instruction" style="display: none;" class="fullscreen-instruction">
        Duplo clique para sair ou pressione ESC
    </div>
    
    <!-- Passar dados para JavaScript -->
    <script>
        // Configuração para o JavaScript
        window.monitorConfig = {
            quadrants: {{ $quadrants }},
            columns: {{ $columns }},
            rows: {{ $rows }},
            connectionConfig: {!! json_encode($connectionConfig) !!},
            pdvData: {!! json_encode($pdvData) !!}
        };
    </script>
</div>