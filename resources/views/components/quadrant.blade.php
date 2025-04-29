@props(['position', 'pdvData' => null])

<div id="quadrant{{ $position }}" class="stream-container" data-quadrant="{{ $position }}">
    <div class="video-container">
        <video id="remoteVideo{{ $position }}" autoplay muted playsinline></video>
        <div id="status{{ $position }}" class="status-indicator">Aguardando conexão...</div>
        
        {{-- Botão de fullscreen para o quadrante --}}
        {{-- <button class="fullscreen-btn" onclick="toggleQuadrantFullscreen(this.closest('.stream-container'))">
            <i class="fas fa-expand"></i>
        </button> --}}
    </div>
    
    <div class="log-container">
        <div id="log{{ $position }}" class="log-content"></div>
    </div>
</div>