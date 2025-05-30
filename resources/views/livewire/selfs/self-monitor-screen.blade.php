<div>
    <div class="monitor-header">
        <h1 class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center">
                {{ $pageTitle }}
            </div>
            
            <div class="d-flex align-items-center">
                <div class="dropdown d-inline-block mr-2">
                    <button id="qualityDropdownBtn" type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-cog"></i> Qualidade
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="qualityDropdownBtn">
                        <h6 class="dropdown-header">Qualidade do vídeo</h6>
                        <a class="dropdown-item {{ $videoQuality == 'low' ? 'active' : '' }}" href="#" onclick="changeGlobalVideoQuality('low'); return false;">Baixa</a>
                        <a class="dropdown-item {{ $videoQuality == 'medium-low' ? 'active' : '' }}" href="#" onclick="changeGlobalVideoQuality('medium-low'); return false;">Média-Baixa</a>
                        <a class="dropdown-item {{ $videoQuality == 'medium' ? 'active' : '' }}" href="#" onclick="changeGlobalVideoQuality('medium'); return false;">Média</a>
                        <a class="dropdown-item {{ $videoQuality == 'high' ? 'active' : '' }}" href="#" onclick="changeGlobalVideoQuality('high'); return false;">Alta</a>
                    </div>
                    <button id="browserFullscreenBtn btn btn-dark" class="btn btn-dark" onclick="toggleBrowserFullscreen()">
                        <i class="fas fa-desktop"></i> {{ $isBrowserFullscreen ? 'Sair da Tela Cheia' : 'Tela Cheia (F11)' }}
                    </button>
                </div>
                
                
            </div>
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
            // Sistema de fila de alertas (deixamos esta inicialização para o arquivo principal)
            if (!window.alertSystem) {
                window.alertSystem = {
                    queue: [],           // Fila de posições com alertas pendentes
                    activeAlert: null,   // Alerta atualmente sendo exibido em fullscreen
                    alertingLogs: new Set() // Mantemos o set para rastrear todos os logs em alerta
                };
            }
            
            // IMPORTANTE: Remover inicialização do sistema antigo
            // if (!window.alertingLogs) {
            //     window.alertingLogs = new Set();
            // }
            
            // Sobrescrever a função stopAlert para usar apenas o novo sistema
            window.stopAlert = function(position) {
                const logContainer = document.getElementById(`log${position}`);
                if (logContainer) {
                    logContainer.classList.remove('inactivity-alert-blink');
                }
                
                // Remove da lista de alertas ativos (usando apenas o novo sistema)
                if (window.alertSystem) {
                    window.alertSystem.alertingLogs.delete(position);
                    
                    // Se este era o alerta ativo no momento, fecha o fullscreen
                    if (window.alertSystem.activeAlert === position) {
                        window.alertSystem.activeAlert = null;
                        
                        // Sai do modo fullscreen do quadrante
                        const quadrant = document.getElementById(`quadrant${position}`);
                        if (quadrant && quadrant.classList.contains('fullscreen')) {
                            toggleQuadrantFullscreen(quadrant);
                        }
                        
                        // Processa o próximo alerta da fila, se houver
                        setTimeout(processNextAlert, 500);
                    }
                    
                    // Se o alerta está na fila, remove-o
                    window.alertSystem.queue = window.alertSystem.queue.filter(pos => pos !== position);
                }
            };
            
            // Função para processar o próximo alerta da fila
            function processNextAlert() {
                if (!window.alertSystem) return;
                
                // Se já temos um alerta ativo ou a fila está vazia, não fazemos nada
                if (window.alertSystem.activeAlert !== null || window.alertSystem.queue.length === 0) {
                    return;
                }
                
                // Pegamos o próximo alerta da fila
                const nextPosition = window.alertSystem.queue.shift();
                
                // Definimos como alerta ativo
                window.alertSystem.activeAlert = nextPosition;
                
                // Colocamos o quadrante em fullscreen
                const quadrant = document.getElementById(`quadrant${nextPosition}`);
                if (quadrant && !quadrant.classList.contains('fullscreen')) {
                    toggleQuadrantFullscreen(quadrant);
                }
            }
            
            // Função para adicionar um alerta usando apenas o novo sistema
            function addAlert(position) {
                if (!window.alertSystem) return;
                
                // Adiciona à lista de logs com alerta (para o efeito visual)
                window.alertSystem.alertingLogs.add(position);
                
                // Aplica a classe de animação
                const logContainer = document.getElementById(`log${position}`);
                if (logContainer) {
                    logContainer.classList.add('inactivity-alert-blink');
                }
                
                // Se já temos um alerta ativo, adiciona à fila
                if (window.alertSystem.activeAlert !== null) {
                    if (!window.alertSystem.queue.includes(position)) {
                        window.alertSystem.queue.push(position);
                    }
                    return;
                }
                
                // Se não temos alerta ativo, este se torna o ativo
                window.alertSystem.activeAlert = position;
                
                // Coloca o quadrante em fullscreen
                const quadrant = document.getElementById(`quadrant${position}`);
                if (quadrant && !quadrant.classList.contains('fullscreen')) {
                    toggleQuadrantFullscreen(quadrant);
                }
            }
            
            // Reaplicamos alertas visuais após atualizações do DOM
            function applyAlerts() {
                if (!window.alertSystem) return;
                
                window.alertSystem.alertingLogs.forEach(position => {
                    const logContainer = document.getElementById(`log${position}`);
                    if (logContainer) {
                        logContainer.classList.add('inactivity-alert-blink');
                    }
                });
            }
            
            // IMPORTANTE: Atualizar o listener para usar o novo sistema
            window.addEventListener('inactivity-alert', function(e) {
                const { position } = e.detail;
                addAlert(position);
            });
            
            Livewire.hook('message.processed', (message, component) => {
                applyAlerts();
            });
        });
    </script>
</div>