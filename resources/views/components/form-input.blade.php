<div class="form-group {{ $attributes->get('class') }}">
    <label for="{{ $name }}">{{ $label }}</label>
    <div class="input-group">
        <input type="{{ $type }}" 
               class="form-control @error($modelName) is-invalid @enderror" 
               id="{{ $name }}" 
               wire:model.lazy="{{ $modelName }}" 
               placeholder="{{ $placeholder }}" 
               {{ $required ? 'required' : '' }}
               {{ $attributes }}>
        
        @if($slot->isNotEmpty())
        <div class="input-group-append">
            {{ $slot }}
        </div>
        @endif
    </div>
    
    @error($modelName)
        <span class="invalid-feedback" style="display: unset;" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>