<div class="video-container">
    <div class="video-placeholder">
        <i class="fas fa-video-slash"></i>
        <span>Sem Sinal de Vídeo</span>
    </div>
    
    <video 
        id="{{ $getVideoId() }}" 
        data-pdv-nome="{{ $pdvNome }}"
        data-rtsp-url="{{ $rtspUrl }}"
        autoplay 
        playsinline 
        muted
        style="display: none;"
    ></video>
    
    <div 
        class="video-overlay" 
        id="{{ $getStatusId() }}"
    >
        {{ $pdvNome }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoConfig = {
                quadrantIndex: {{ $quadrantIndex }},
                pdvNome: "{{ $pdvNome }}",
                rtspUrl: "{{ $rtspUrl }}"
            };
            
            if (window.initVideo) {
                window.initVideo(videoConfig);
            }
        });
    </script>
</div>