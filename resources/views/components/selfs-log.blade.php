<div class="log-container" id="{{ $getLogContainerId() }}" data-pdv-ip="{{ $pdvIp }}" data-pdv-nome="{{ $pdvNome }}">
    <div class="log-content">
        <div class="log-placeholder">Aguardando dados do PDV...</div>
        
        @if($errors->has('log'))
            <div class="error-message">
                {{ $errors->first('log') }}
            </div>
        @endif
    </div>
    
    <div class="pdv-notification" style="display: none;"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logConfig = {
            quadrantIndex: {{ $quadrantIndex }},
            pdvIp: "{{ $pdvIp }}",
            pdvNome: "{{ $pdvNome }}"
        };
        
        if (window.initLog) {
            window.initLog(logConfig);
        }
    });
</script>