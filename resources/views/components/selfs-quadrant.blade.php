<div 
    class="stream-container" 
    id="{{ $getQuadrantId() }}" 
    data-pdv-id="{{ $pdvData['id'] ?? '' }}"
    data-pdv-nome="{{ $pdvData['nome'] ?? '' }}"
>
    {{ $slot }}
</div>