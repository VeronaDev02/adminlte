<div>
    <div class="monitor-header">
        <h1>{{ $pageTitle }}
            <button id="browserFullscreenBtn" class="title-button" onclick="toggleBrowserFullscreen()">
                <i class="fas fa-desktop"></i> {{ $isBrowserFullscreen ? 'Sair da Tela Cheia' : 'Tela Cheia (F11)' }}
            </button>
        </h1>
    </div>
    
    <div class="monitor-grid-container" style="grid-template-columns: repeat({{ $columns }}, 1fr); grid-template-rows: repeat({{ $rows }}, 1fr);">
        @for($i = 1; $i <= $quadrants; $i++)
            @php 
                $pdvInfo = isset($pdvData[$i]) ? $pdvData[$i] : null;
                $pdvName = $pdvInfo ? $pdvInfo['nome'] : 'Sem PDV';
                $pdvCode = $pdvInfo && !empty($pdvInfo['pdvCodigo']) ? substr($pdvInfo['pdvCodigo'], -2) : '--';
                $status = isset($pdvStatus[$i]) ? $pdvStatus[$i] : ['message' => 'Desconectado', 'class' => ''];
                $logs = isset($pdvLogs[$i]) ? $pdvLogs[$i] : [];
            @endphp
            <div id="quadrant{{ $i }}" class="stream-container {{ $activeFullscreenQuadrant == $i ? 'fullscreen' : '' }}">
                <div class="video-container">
                    <video id="remoteVideo{{ $i }}" autoplay muted playsinline></video>
                    <span id="status{{ $i }}" class="status-indicator {{ $status['class'] }}">PDV {{ $pdvCode }}</span>
                </div>
                
                <div id="log{{ $i }}" class="log-container">
                    <div class="log-header">
                        {{-- <strong>PDV {{ $pdvCode }}</strong> --}}
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
    
    <script>
        window.monitorConfig = {
            quadrants: {{ $quadrants }},
            columns: {{ $columns }},
            rows: {{ $rows }},
            connectionConfig: {!! json_encode($connectionConfig) !!},
            pdvData: {!! json_encode($pdvData) !!}
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.alertingLogs) {
                window.alertingLogs = new Set();
            }
            
            // Esta função é chamada pelo toggleQuadrantFullscreen para parar o alerta
            window.stopAlert = function(position) {
                window.alertingLogs.delete(position);
                const logContainer = document.getElementById(`log${position}`);
                if (logContainer) {
                    logContainer.classList.remove('inactivity-alert-blink');
                }
            };
            
            // Este evento é disparado pelo Livewire (handleInactivityAlert) e adiciona a classe de animação
            window.addEventListener('inactivity-alert', function(e) {
                const { position } = e.detail;
                window.alertingLogs.add(position);
                
                const logContainer = document.getElementById(`log${position}`);
                if (logContainer) {
                    logContainer.classList.add('inactivity-alert-blink');
                }
            });
        });
    </script>
</div>