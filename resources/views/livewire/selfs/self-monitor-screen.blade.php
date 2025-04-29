<div>
    <div class="monitor-grid-container" style="grid-template-columns: repeat({{ $columns }}, 1fr); grid-template-rows: repeat({{ $rows }}, 1fr);">
        @for($i = 1; $i <= $quadrants; $i++)
            @php 
                $pdvInfo = isset($pdvData[$i]) ? $pdvData[$i] : null;
            @endphp
            <x-quadrant :position="$i" :pdvData="$pdvInfo" />
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