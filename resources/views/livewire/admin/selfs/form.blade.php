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
                            <label for="sel_pdv_ip">Endereço IP do PDV</label>
                            <input type="text" class="form-control @error('sel_pdv_ip') is-invalid @enderror" 
                                   id="sel_pdv_ip" wire:model.lazy="sel_pdv_ip" 
                                   placeholder="Digite o endereço IP do PDV" required>
                            @error('sel_pdv_ip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_dvr_ip">Endereço IP do DVR</label>
                            <input type="text" class="form-control @error('sel_dvr_ip') is-invalid @enderror" 
                                   id="sel_dvr_ip" wire:model.lazy="sel_dvr_ip" 
                                   placeholder="Digite o endereço IP do DVR" required>
                            @error('sel_dvr_ip')
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
                            <label for="sel_dvr_username">Usuário do DVR</label>
                            <input type="text" class="form-control @error('sel_dvr_username') is-invalid @enderror" 
                                   id="sel_dvr_username" wire:model.lazy="sel_dvr_username" 
                                   placeholder="Digite o usuário do DVR" required>
                            @error('sel_dvr_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel_dvr_password">Senha do DVR</label>
                            <input type="password" class="form-control @error('sel_dvr_password') is-invalid @enderror" 
                                   id="sel_dvr_password" wire:model.lazy="sel_dvr_password" 
                                   placeholder="Digite a senha do DVR" required>
                            @error('sel_dvr_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sel_camera_canal">Canal da Câmera</label>
                            <input type="text" class="form-control @error('sel_camera_canal') is-invalid @enderror" 
                                   id="sel_camera_canal" wire:model.lazy="sel_camera_canal" 
                                   placeholder="Digite o canal da câmera" required>
                            @error('sel_camera_canal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sel_dvr_porta">Porta do DVR</label>
                            <input type="text" class="form-control @error('sel_dvr_porta') is-invalid @enderror" 
                                   id="sel_dvr_porta" wire:model.lazy="sel_dvr_porta" 
                                   placeholder="Digite a porta do DVR" required>
                            @error('sel_dvr_porta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sel_rtsp_path">Caminho RTSP</label>
                            <input type="text" class="form-control @error('sel_rtsp_path') is-invalid @enderror" 
                                   id="sel_rtsp_path" wire:model.lazy="sel_rtsp_path" 
                                   placeholder="Digite o caminho RTSP" required>
                            @error('sel_rtsp_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>URL RTSP Gerada</label>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       id="generated-rtsp-url"
                                       value="{{ $sel_rtsp_url ?? 'Preencha todos os campos' }}" 
                                       readonly>
                                <div class="input-group-append mr-2">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="copyToClipboard(this)">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="input-group-append">
                                    <button type="button" 
                                            wire:click="generateRtspUrl" 
                                            class="btn btn-primary"
                                            :disabled="!sel_dvr_username || !sel_dvr_password || !sel_dvr_ip || !sel_dvr_porta || !sel_rtsp_path || !sel_camera_canal">
                                        <i class="fas fa-magic"></i> Gerar URL RTSP
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                URL gerada automaticamente com base nos campos preenchidos
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="sel_rtsp_url">RTSP URL (opcional)</label>
                            <input type="text" class="form-control @error('sel_rtsp_url') is-invalid @enderror" 
                                   id="sel_rtsp_url" wire:model.lazy="sel_rtsp_url" 
                                   placeholder="Insira uma URL RTSP personalizada ou deixe em branco para usar a gerada">
                            @error('sel_rtsp_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Se deixado em branco, será usada a URL RTSP gerada automaticamente
                            </small>
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
                $('#sel_pdv_ip, #sel_dvr_ip').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                    translation: {
                        'Z': {pattern: /[0-9]/, optional: true}
                    }
                });
            });
        }
        
        document.addEventListener('rtsp-url-generated', function(event) {
            const generatedUrlInput = document.getElementById('generated-rtsp-url');
            generatedUrlInput.value = event.detail.url;
        });

        document.addEventListener('rtsp-url-generation-failed', function() {
            toastr.warning('Preencha todos os campos necessários para gerar a URL RTSP');
        });
        
        function copyToClipboard(btn) {
            const input = btn.closest('.input-group').querySelector('input');
            navigator.clipboard.writeText(input.value).then(() => {
                btn.innerHTML = '<i class="fas fa-check text-success"></i>';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy"></i>';
                }, 2000);
            });
        }
    </script>
    @stop
</div>