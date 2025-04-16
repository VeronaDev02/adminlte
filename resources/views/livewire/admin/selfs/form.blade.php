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
        <form wire:submit.prevent="save" id="selfForm">
            <div class="row">
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sel_pdv_ip">Endereço IP do PDV</label>
                        <input type="text" 
                               class="form-control @error('sel_pdv_ip') is-invalid @enderror" 
                               id="sel_pdv_ip" 
                               wire:model.lazy="sel_pdv_ip" 
                               placeholder="Digite o endereço IP do PDV" 
                               required>
                        @error('sel_pdv_ip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

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
                        <label for="sel_uni_id">Unidade</label>
                        <select class="form-control @error('sel_uni_id') is-invalid @enderror" 
                                id="sel_uni_id" 
                                wire:model.lazy="sel_uni_id" 
                                required>
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
                <div class="col-md-6">
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
            </div>

            <div class="row">
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="path_rtsp">
                            Caminho RTSP (ex: cam/realmonitor)
                            <span class="badge badge-info">Variável: {path_rtsp}</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="path_rtsp" 
                               placeholder="Ex: cam/realmonitor">
                        <small class="form-text text-muted">
                            Campo auxiliar, usado apenas na geração do template abaixo.
                        </small>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="sel_rtsp_path">
                            URL RTSP Completa
                            <small class="text-muted">(Este campo será salvo no banco de dados)</small>
                        </label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control @error('sel_rtsp_path') is-invalid @enderror" 
                                   id="sel_rtsp_path" 
                                   wire:model.lazy="sel_rtsp_path"
                                   placeholder="rtsp://usuario:senha@ip:porta/caminho?channel=canal&subtype=0">
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
                            Você pode usar o gerador abaixo para criar a URL ou inserir manualmente neste campo.
                        </small>
                    </div>
                </div>
            </div>

            <div class="card mt-4 mb-4 border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Gerador de URL RTSP</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        Variáveis disponíveis: {login_dvr}, {senha_dvr}, {ip_dvr}, {porta_dvr}, {path_rtsp}, {canal_rtsp}
                    </p>
                    
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">rtsp://</span>
                            </div>
                            <textarea id="rtsp-template-editor" class="form-control" rows="3" style="font-family: monospace;">{login_dvr}:{senha_dvr}@{ip_dvr}:{porta_dvr}/{path_rtsp}?channel={canal_rtsp}&subtype=0</textarea>
                        </div>
                        <small class="form-text text-muted mt-2">
                            Você pode modificar o template acima como desejar, alterando a ordem ou inserindo texto personalizado.
                        </small>
                    </div>
                    
                    <div class="mt-3 d-flex">
                        <button type="button" class="btn btn-primary" id="btn-apply-template">
                            <i class="fas fa-magic"></i> Gerar URL
                        </button>
                        <button type="button" class="btn btn-outline-secondary ml-2" id="btn-reset-template">
                            <i class="fas fa-undo"></i> Restaurar Padrão
                        </button>
                        <button type="button" class="btn btn-outline-success ml-auto" id="btn-copy-to-url">
                            <i class="fas fa-arrow-up"></i> Copiar para URL RTSP
                        </button>
                    </div>
                    
                    <div class="mt-3">
                        <label>URL Gerada:</label>
                        <input type="text" class="form-control" id="generated-url" readonly>
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
                            id="btn-save"
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
        initTemplateEditor();
        document.getElementById('path_rtsp').value = '';
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
    
    function initTemplateEditor() {
        const DEFAULT_TEMPLATE = '{login_dvr}:{senha_dvr}@{ip_dvr}:{porta_dvr}/{path_rtsp}?channel={canal_rtsp}&subtype=0';
        const templateEditor = document.getElementById('rtsp-template-editor');
        const generatedUrl = document.getElementById('generated-url');
        const rtspUrlField = document.getElementById('sel_rtsp_path');
        const pathRtspField = document.getElementById('path_rtsp');
        
        document.getElementById('btn-apply-template').addEventListener('click', function() {
            generateUrlFromTemplate(templateEditor.value);
        });
        
        document.getElementById('btn-reset-template').addEventListener('click', function() {
            templateEditor.value = DEFAULT_TEMPLATE;
        });
        
        document.getElementById('btn-copy-to-url').addEventListener('click', function() {
            if (generatedUrl.value) {
                @this.set('sel_rtsp_path', generatedUrl.value);
                rtspUrlField.value = generatedUrl.value;
            }
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
                return;
            }
            
            let url = 'rtsp://' + template;
            
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
            
            generatedUrl.value = url;
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
</script>
@stop