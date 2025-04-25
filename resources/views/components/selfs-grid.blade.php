<div>
    <style>
        {{ $getResponsiveGridStyle() }}
    </style>
    <div 
        class="stream-grid {{ $getGridClass() }}" 
        id="mainGrid" 
        style="
            grid-template-columns: repeat({{ $cols }}, 1fr);
            grid-template-rows: repeat({{ $rows }}, 1fr);
        "
    >
        @php $cellIndex = 0; @endphp
        
        @for($row = 0; $row < $rows; $row++)
            @for($col = 0; $col < $cols; $col++)
                @if($cellIndex < count($pdvDataList) && $cellIndex < $activeQuadrants)
                    <x-selfs-quadrant 
                        :pdvData="$pdvDataList[$cellIndex]" 
                        :quadrantIndex="$cellIndex + 1"
                    >
                        <x-selfs-log 
                            :pdvIp="$pdvDataList[$cellIndex]['pdvIp']" 
                            :quadrantIndex="$cellIndex + 1"
                            :pdvNome="$pdvDataList[$cellIndex]['nome']"
                        />
                        <x-selfs-video 
                            :rtspUrl="$pdvDataList[$cellIndex]['rtspUrl']" 
                            :quadrantIndex="$cellIndex + 1"
                            :pdvNome="$pdvDataList[$cellIndex]['nome']"
                        />
                    </x-selfs-quadrant>
                    @php $cellIndex++; @endphp
                @endif
            @endfor
        @endfor
    </div>
    
    <script>
        const pdvData = @json($pdvDataList);
        const serverConfig = @json($serverConfig);
        const activeQuadrants = {{ $activeQuadrants }};
        const gridCols = {{ $cols }};
        const gridRows = {{ $rows }};
    </script>
</div>

@push('css')
<style>
.stream-grid {
    display: grid;
    gap: 1px;
    background-color: #000000;
    border: 1px solid #333;
    min-height: calc(100vh - 250px);
    height: 100%;
    width: 100%;
}

.stream-grid.selfs-grid-custom {

}

.stream-container {
    display: flex;
    background-color: #000000;
    max-height: 100%;
    overflow: hidden;
    aspect-ratio: 16/9;
    min-height: 200px;
}

.log-container {
    width: 30%;
    background-color: #5a636a;
    border-right: 1px solid #333;
    position: relative;
    height: 100%;
}

.log-placeholder {
    color: #888;
    font-style: italic;
    padding: 10px;
    text-align: center;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
}

.video-container {
    width: 70%;
    background-color: #000;
    position: relative;
    height: 100%;
}

.video-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #aaa;
    font-size: 1.2em;
    background-color: #111;
}

.video-placeholder i {
    font-size: 2em;
    margin-bottom: 10px;
    opacity: 0.7;
}

.log-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow-y: auto;
    padding: 5px;
    font-family: "Courier New", monospace;
    font-size: 11px;
    color: #ddd;
}

.video-overlay {
    position: absolute;
    top: 5px;
    left: 5px;
    background-color: rgba(0,0,0,0.7);
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.7em;
    z-index: 10;
}

.pdv-notification {
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(255, 0, 0, 0.8);
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 14px;
    z-index: 100;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
    text-align: center;
    max-width: 90%;
    display: none;
}

@media (max-width: 768px) {
    .stream-container {
        flex-direction: column;
        min-height: 400px;
    }
    
    .log-container, .video-container {
        width: 100%;
        height: 50%;
    }
}
</style>
@endpush