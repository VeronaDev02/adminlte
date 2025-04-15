<div>
    @section('title', $title)
    
    @section('content_header')
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mx-auto" style="font-weight: bold;">{{ $title }}</h1>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('selfs.index') }}">SelfCheckouts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        </div>
    @stop
    
    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_name">Nome do SelfCheckout</label>
                            <input type="text" class="form-control @error('sel_name') is-invalid @enderror" 
                                   id="sel_name" wire:model.lazy="sel_name" 
                                   placeholder="Digite o nome do SelfCheckout" required>
                            @error('sel_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_pdv_ip">Endereço IP</label>
                            <input type="text" class="form-control @error('sel_pdv_ip') is-invalid @enderror" 
                                   id="sel_pdv_ip" wire:model.lazy="sel_pdv_ip" 
                                   placeholder="Digite o endereço IP" required>
                            @error('sel_pdv_ip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_rtsp_url">URL RTSP</label>
                            <input type="text" class="form-control @error('sel_rtsp_url') is-invalid @enderror" 
                                   id="sel_rtsp_url" wire:model.lazy="sel_rtsp_url" 
                                   placeholder="Digite a URL RTSP">
                            @error('sel_rtsp_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_uni_id">Unidade</label>
                            <select class="form-control @error('sel_uni_id') is-invalid @enderror" 
                                    id="sel_uni_id" wire:model="sel_uni_id" required>
                                <option value="">Selecione uma Unidade</option>
                                @foreach($unidades as $unidade)
                                    <option value="{{ $unidade->uni_id }}">
                                        {{ $unidade->uni_codigo }} - {{ $unidade->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sel_uni_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_status">Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="sel_status" wire:model="sel_status">
                                <label class="custom-control-label" for="sel_status">
                                    {{ $sel_status ? 'Ativo' : 'Inativo' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" 
                                class="btn btn-primary" 
                                wire:loading.attr="disabled" 
                                wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save"></i> Salvar
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin"></i> Salvando...
                            </span>
                        </button>
                        <a href="{{ route('selfs.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupIpMask();
        });
        
        function setupIpMask() {
            $(document).ready(function(){
                $('#sel_pdv_ip').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                    translation: {
                        'Z': {pattern: /[0-9]/, optional: true}
                    }
                });
            });
        }
        
        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', (message, component) => {
                setupIpMask();
            });
        });
    </script>
    @stop
</div>