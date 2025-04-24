@section('title', $title)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('selfs.index') }}">SelfCheckouts</a></li>
                <li class="breadcrumb-item active" style="font-weight: normal;">{{ $title }}</li>
            </ol>
        </div>
    </div>
@stop

<div class="card">
    <div class="card-header">
        <div class="row">
            <h3 class="col-md-10">{{ $title }}</h3>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="save" id="selfForm">
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="sel_status">Status</label>
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" 
                                   id="sel_status" wire:model="sel_status">
                            <label class="custom-control-label" for="sel_status">
                                {{ $sel_status ? 'Ativo' : 'Inativo' }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="sel_name">Nome do SelfCheckout</label>
                        <input type="text" 
                               class="form-control @error('sel_name') is-invalid @enderror" 
                               id="sel_name" 
                               wire:model.lazy="sel_name" 
                               placeholder="Digite o nome do SelfCheckout" 
                               required>
                        @error('sel_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="sel_pdv_codigo">Código PDV</label>
                        <input type="text" 
                            class="form-control @error('sel_pdv_codigo') is-invalid @enderror" 
                            id="sel_pdv_codigo" 
                            wire:model.lazy="sel_pdv_codigo" 
                            placeholder="Máx 3" 
                            maxlength="3">
                        @error('sel_pdv_codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="sel_pdv_ip">IP do PDV</label>
                        <input type="text" 
                               class="form-control @error('sel_pdv_ip') is-invalid @enderror" 
                               id="sel_pdv_ip" 
                               wire:model.lazy="sel_pdv_ip" 
                               placeholder="Digite o IP do PDV" 
                               required>
                        @error('sel_pdv_ip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sel_uni_id">Unidade</label>
                        <div wire:model.lazy="sel_uni_id">
                            <select class="form-control @error('sel_uni_id') is-invalid @enderror" 
                                    id="sel_uni_id" required>
                                <option value="">Selecione uma Unidade</option>
                                @foreach($unidades as $unidade)
                                    <option value="{{ $unidade->uni_id }}" {{ $sel_uni_id == $unidade->uni_id ? 'selected' : '' }}>
                                        {{ $unidade->uni_codigo }} - {{ $unidade->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('sel_uni_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mt-4 mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Configurações da Câmera/DVR</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="sel_dvr_ip">
                                    Endereço IP do DVR 
                                    <span class="badge badge-info">Variável: {ip_dvr}</span>
                                </label>
                                <input type="text" 
                                    class="form-control @error('sel_dvr_ip') is-invalid @enderror" 
                                    id="sel_dvr_ip" 
                                    wire:model.lazy="sel_dvr_ip" 
                                    placeholder="Digite o endereço IP do DVR" 
                                    required>
                                @error('sel_dvr_ip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sel_dvr_porta">
                                    Porta do DVR
                                    <span class="badge badge-info">Variável: {porta_dvr}</span>
                                </label>
                                <input type="text" 
                                    class="form-control @error('sel_dvr_porta') is-invalid @enderror" 
                                    id="sel_dvr_porta" 
                                    wire:model.lazy="sel_dvr_porta" 
                                    placeholder="Digite a porta do DVR" 
                                    required>
                                @error('sel_dvr_porta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sel_camera_canal">
                                    Canal da Câmera
                                    <span class="badge badge-info">Variável: {canal_rtsp}</span>
                                </label>
                                <input type="text" 
                                    class="form-control @error('sel_camera_canal') is-invalid @enderror" 
                                    id="sel_camera_canal" 
                                    wire:model.lazy="sel_camera_canal" 
                                    placeholder="Digite o canal da câmera" 
                                    required>
                                @error('sel_camera_canal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sel_dvr_username">
                                    Usuário do DVR
                                    <span class="badge badge-info">Variável: {login_dvr}</span>
                                </label>
                                <input type="text" 
                                    class="form-control @error('sel_dvr_username') is-invalid @enderror" 
                                    id="sel_dvr_username" 
                                    wire:model.lazy="sel_dvr_username" 
                                    placeholder="Digite o usuário do DVR" 
                                    required>
                                @error('sel_dvr_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sel_dvr_password">
                                    Senha do DVR
                                    <span class="badge badge-info">Variável: {senha_dvr}</span>
                                </label>
                                <input type="password" 
                                    class="form-control @error('sel_dvr_password') is-invalid @enderror" 
                                    id="sel_dvr_password" 
                                    wire:model.lazy="sel_dvr_password" 
                                    placeholder="Digite a senha do DVR" 
                                    required>
                                @error('sel_dvr_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="path_rtsp">
                                    Caminho RTSP
                                    <span class="badge badge-info">Variável: {path_rtsp}</span>
                                </label>
                                <input type="text" 
                                    class="form-control" 
                                    id="path_rtsp" 
                                    placeholder="Ex: cam/realmonitor">
                                <small class="form-text text-muted">
                                    Campo auxiliar para template
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4 mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">URL RTSP</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        <strong>Template RTSP:</strong> Configure o formato da URL utilizando as variáveis disponíveis:
                    </p>
                    
                    <div class="form-group">
                        <textarea id="rtsp-template-editor" class="form-control" rows="3" style="font-family: monospace;">rtsp://{login_dvr}:{senha_dvr}@{ip_dvr}:{porta_dvr}/{path_rtsp}?channel={canal_rtsp}&subtype=0</textarea>
                        <small class="form-text text-muted mt-2">
                            Variáveis disponíveis: {login_dvr}, {senha_dvr}, {ip_dvr}, {porta_dvr}, {path_rtsp}, {canal_rtsp}
                        </small>
                    </div>
                    
                    <div class="mb-4 d-flex">
                        <button type="button" class="btn btn-primary" id="btn-apply-template">
                            <i class="fas fa-magic"></i> Gerar URL RTSP
                        </button>
                        <button type="button" class="btn btn-outline-secondary ml-2" id="btn-reset-template">
                            <i class="fas fa-undo"></i> Restaurar Template Padrão
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="sel_rtsp_path">URL Gerada</label>
                                <div class="input-group">
                                    <input type="text" 
                                        class="form-control @error('sel_rtsp_path') is-invalid @enderror" 
                                        id="generated-url" 
                                        wire:model.lazy="sel_rtsp_path"
                                        placeholder="rtsp://usuario:senha@ip:porta/caminho?channel=canal&subtype=0"
                                        readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="copyToClipboard(this)">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('sel_rtsp_path')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Esta URL será salva no banco de dados.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" 
                            class="btn btn-primary" 
                            id="btn-save"
                            wire:loading.attr="disabled" 
                            wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            @if($isEdit)
                                <i class="fas fa-pen mr-1"></i> Atualizar
                            @else
                                <i class="fas fa-save mr-1"></i> Salvar
                            @endif
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

@section('css')
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
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        aplicarMascaras();

        $('#sel_uni_id').select2({
            placeholder: 'Selecione uma Unidade',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        })
        
        initTemplateEditor();
        
        document.getElementById('path_rtsp').value = '';
    });
    
    function aplicarMascaras() {
        $('#sel_pdv_ip, #sel_dvr_ip').mask('999.999.999.999', {
            placeholder: "___.___.___.___"
        });
        
        $('#sel_pdv_codigo').mask('999', {
            placeholder: '___'
        });
    }
    
    function initTemplateEditor() {
        const DEFAULT_TEMPLATE = 'rtsp://{login_dvr}:{senha_dvr}@{ip_dvr}:{porta_dvr}/{path_rtsp}?channel={canal_rtsp}&subtype=0';
        const templateEditor = document.getElementById('rtsp-template-editor');
        const generatedUrl = document.getElementById('generated-url');
        const pathRtspField = document.getElementById('path_rtsp');
        
        if (generatedUrl.value) {
            document.getElementById('generated-url').readOnly = false;
        }
        
        document.getElementById('btn-apply-template').addEventListener('click', function() {
            generateUrlFromTemplate(templateEditor.value);
        });
        
        document.getElementById('btn-reset-template').addEventListener('click', function() {
            templateEditor.value = DEFAULT_TEMPLATE;
        });
        
        function generateUrlFromTemplate(template) {
            const requiredFields = [
                'sel_dvr_username',
                'sel_dvr_password',
                'sel_dvr_ip',
                'sel_dvr_porta',
                'sel_camera_canal'
            ];
            
            let allFieldsFilled = true;
            let missingFields = [];
            
            requiredFields.forEach(field => {
                const fieldValue = document.querySelector(`#${field}`).value;
                if (!fieldValue) {
                    allFieldsFilled = false;
                    missingFields.push(field);
                }
            });
            
            if (!allFieldsFilled) {
                toastr.warning('Preencha todos os campos obrigatórios para gerar a URL RTSP.');
                return;
            }
            
            let url = template;
            
            const variables = {
                '{login_dvr}': document.querySelector('#sel_dvr_username').value,
                '{senha_dvr}': document.querySelector('#sel_dvr_password').value,
                '{ip_dvr}': document.querySelector('#sel_dvr_ip').value,
                '{porta_dvr}': document.querySelector('#sel_dvr_porta').value,
                '{path_rtsp}': document.querySelector('#path_rtsp').value || 'cam/realmonitor',
                '{canal_rtsp}': document.querySelector('#sel_camera_canal').value
            };
            
            Object.keys(variables).forEach(variable => {
                url = url.split(variable).join(variables[variable]);
            });
            
            document.getElementById('generated-url').readOnly = false;
            document.getElementById('generated-url').value = url;
            @this.set('sel_rtsp_path', url);
            document.getElementById('generated-url').readOnly = true;
            
            toastr.success('URL RTSP gerada com sucesso!');
        }
    }
    
    function copyToClipboard(btn) {
        const input = btn.closest('.input-group').querySelector('input');
        navigator.clipboard.writeText(input.value).then(() => {
            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-copy"></i>';
            }, 2000);
        });
    }
    
    document.addEventListener('livewire:load', function () {
        window.addEventListener('toastr:success', event => {
            toastr.success(event.detail.message);
        });
        
        window.addEventListener('toastr:error', event => {
            toastr.error(event.detail.message);
        });
        
        window.addEventListener('contentChanged', function() {
            aplicarMascaras();
            $('#sel_uni_id').trigger('change');
        });
    });
</script>
@stop