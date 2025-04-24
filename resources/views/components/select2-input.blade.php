<div class="form-group {{ $attributes->get('class') }}">
    <label for="{{ $name }}">{{ $label }}</label>
    <div wire:ignore>
        <select class="form-control select2 @error($name) is-invalid @enderror" 
                id="{{ $name }}" 
                {{ $required ? 'required' : '' }}
                {{ $attributes }}>
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $value => $label)
                <option value="{{ $value }}" 
                        {{ $selectedValue == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        display: flex;
        align-items: center;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-top: 0;
        padding-bottom: 0;
        display: flex;
        align-items: center;
        height: 100%;
        padding-right: 45px; 
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        display: flex;
        align-items: center;
        right: 5px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute;
        right: 25px; 
        margin-right: 0;
        height: 100%;
        display: flex;
        align-items: center;
        font-size: 18px; 
        color: #777;
        font-weight: normal;
    }
    
    .select2-results__option {
        padding: 8px 12px;
    }

    /* Força a exibição do campo de pesquisa */
    .select2-search--dropdown {
        display: block !important;
        padding: 8px;
    }
    
    .select2-search--dropdown .select2-search__field {
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ced4da;
        width: 100%;
        height: 38px;
    }
</style>
@endpush
@endonce

@once
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        inicializarSelect2();
        
        // Adiciona um evento global para o objeto select2
        $(document).on('select2:open', function() {
            setTimeout(function() {
                $('.select2-search__field').focus();
            }, 100);
        });
        
        function inicializarSelect2() {
            try {
                // Primeiro destrói se já foi inicializado
                if ($('#{{ $name }}').data('select2')) {
                    $('#{{ $name }}').select2('destroy');
                }
                
                $('#{{ $name }}').select2({
                    placeholder: '{{ $placeholder }}',
                    allowClear: true,
                    width: '100%',
                    // Configura para sempre mostrar o campo de pesquisa
                    minimumResultsForSearch: 0,
                    language: {
                        noResults: function() {
                            return "Nenhum resultado encontrado";
                        },
                        searching: function() {
                            return "Procurando...";
                        }
                    }
                });
                
                $('#{{ $name }}').on('change', function() {
                    @this.set('{{ $name }}', $(this).val());
                });
            } catch (e) {
                console.error('Erro ao inicializar Select2:', e);
            }
        }
        
        // Suporte para Livewire
        if (window.Livewire) {
            Livewire.hook('message.processed', function() {
                inicializarSelect2();
            });
        }
    });
</script>
@endpush
@endonce