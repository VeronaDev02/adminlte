<div>
    <div class="monitor-header">
        <h1>{{ $pageTitle }}
            <button id="browserFullscreenBtn" class="title-button" onclick="toggleBrowserFullscreen()">
                <i class="fas fa-desktop"></i> {{ $isBrowserFullscreen ? 'Sair da Tela Cheia' : 'Tela Cheia (F11)' }}
            </button>
        </h1>
        <div class="monitor-controls">
            <div id="serverStatus" class="server-status {{ $serverStatusClass }}">
                {{ $serverStatus }}
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
                $status = isset($pdvStatus[$i]) ? $pdvStatus[$i] : ['message' => 'Desconectado', 'class' => ''];
                $logs = isset($pdvLogs[$i]) ? $pdvLogs[$i] : [];
            @endphp
            <div id="quadrant{{ $i }}" class="stream-container {{ $activeFullscreenQuadrant == $i ? 'fullscreen' : '' }}">
                <div class="video-container">
                    <video id="remoteVideo{{ $i }}" autoplay muted playsinline></video>
                    <span id="status{{ $i }}" class="status-indicator {{ $status['class'] }}">{{ $status['message'] }}</span>
                    {{-- <button class="fullscreen-btn" onclick="toggleQuadrantFullscreen(document.getElementById('quadrant{{ $i }}'))">
                        <i class="fas fa-expand"></i>
                    </button> --}}
                </div>
                
                <div id="log{{ $i }}" class="log-container">
                    <div class="log-header">
                        <strong>{{ $pdvName }}</strong>
                        <small>{{ $pdvIp }} ({{ $pdvCode }})</small>
                    </div>
                    <div class="log-content">
                        @foreach($logs as $log)
                            {{ $log }}
                        @endforeach
                    </div>
                </div>
            </div>
        @endfor
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