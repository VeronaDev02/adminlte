<form class="form" id="form_self" method="POST"
    @if ($editMode) action="{{ route('admin.self.update', $self->sel_id) }}" @else action="{{ route('admin.self.store') }}" @endif>
    @csrf
    @if ($editMode)
        @method('PUT')
    @endif
    
    <div class="row">
        <div class="col-1 form-group">
            <label for="sel_status">Ativo</label><br>
            <input class="form-control" id="sel_status" name="sel_status" type="checkbox" value="1"
                @if (!$editMode || ($editMode && $self->sel_status)) checked @endif>
            @error('sel_status')
                <span class="invalid-feedback" style="display: unset;" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-2 form-group">
            <label for="sel_name">Nome do SelfCheckout</label>
            <input type="text" class="form-control" id="sel_name" name="sel_name" placeholder="Nome do SelfCheckout"
                value="{{ old('sel_name', $editMode ? $self->sel_name : '') }}">
            @error('sel_name')
                <span class="invalid-feedback" style="display: unset;" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-2 form-group">
            <label for="sel_pdv_codigo">Código PDV</label>
            <input type="text" class="form-control" id="sel_pdv_codigo" name="sel_pdv_codigo" placeholder="Código PDV" maxlength="3"
                value="{{ old('sel_pdv_codigo', $editMode ? $self->sel_pdv_codigo : '') }}">
            @error('sel_pdv_codigo')
                <span class="invalid-feedback" style="display: unset;" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-2 form-group">
            <label for="sel_pdv_ip">IP do PDV</label>
            <input type="text" class="form-control" id="sel_pdv_ip" name="sel_pdv_ip" placeholder="IP do PDV"
                value="{{ old('sel_pdv_ip', $editMode ? $self->sel_pdv_ip : '') }}">
            @error('sel_pdv_ip')
                <span class="invalid-feedback" style="display: unset;" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-3 form-group">
            <label for="sel_uni_id">Unidade</label>
            <select name="sel_uni_id" id="sel_uni_id" class="form-control">
                <option value="">Selecione uma Unidade</option>
                @forelse ($unidades as $unidade)
                    <option 
                        @if ($editMode && $self->sel_uni_id == $unidade->uni_id) selected @endif 
                        value="{{ $unidade->uni_id }}">
                        {{ $unidade->uni_codigo }} - {{ $unidade->nome }}
                    </option>
                @empty
                    <option>Nenhuma unidade encontrada</option>
                @endforelse
            </select>
            @error('sel_uni_id')
                <span class="invalid-feedback" style="display: unset;" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
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
                            class="form-control" 
                            id="sel_dvr_ip" 
                            name="sel_dvr_ip" 
                            placeholder="Digite o endereço IP do DVR"
                            value="{{ old('sel_dvr_ip', $editMode ? $self->sel_dvr_ip : '') }}">
                        @error('sel_dvr_ip')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
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
                            class="form-control" 
                            id="sel_dvr_porta" 
                            name="sel_dvr_porta" 
                            placeholder="Digite a porta do DVR"
                            value="{{ old('sel_dvr_porta', $editMode ? $self->sel_dvr_porta : '') }}">
                        @error('sel_dvr_porta')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
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
                            class="form-control" 
                            id="sel_camera_canal" 
                            name="sel_camera_canal" 
                            placeholder="Digite o canal da câmera"
                            value="{{ old('sel_camera_canal', $editMode ? $self->sel_camera_canal : '') }}">
                        @error('sel_camera_canal')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
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
                            class="form-control" 
                            id="sel_dvr_username" 
                            name="sel_dvr_username" 
                            placeholder="Digite o usuário do DVR"
                            value="{{ old('sel_dvr_username', $editMode ? $self->sel_dvr_username : '') }}">
                        @error('sel_dvr_username')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
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
                            class="form-control" 
                            id="sel_dvr_password" 
                            name="sel_dvr_password" 
                            placeholder="Digite a senha do DVR"
                            value="{{ old('sel_dvr_password', $editMode ? $self->sel_dvr_password : '') }}">
                        @error('sel_dvr_password')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
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
                                class="form-control" 
                                id="generated-url" 
                                name="sel_rtsp_path"
                                placeholder="rtsp://usuario:senha@ip:porta/caminho?channel=canal&subtype=0"
                                value="{{ old('sel_rtsp_path', $editMode ? $self->sel_rtsp_path : '') }}"
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
            <button type="submit" class="btn btn-primary" id="btn-save">
                @if($editMode)
                    <i class="fas fa-pen mr-1"></i> Atualizar
                @else
                    <i class="fas fa-save mr-1"></i> Salvar
                @endif
            </button>
            <a href="{{ route('admin.self.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</form>

@push('js')
<script>
    $(document).ready(function() {
        $('input[type="checkbox"]').bootstrapSwitch({
            onText: 'SIM',
            offText: 'NÃO'
        });

        $('#sel_uni_id').select2({
            allowClear: false,
            placeholder: "Selecione a Unidade",
        });

        initTemplateEditor();
    });

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
            
            document.getElementById('generated-url').value = url;
            document.getElementById('generated-url').readOnly = false;
            document.getElementById('generated-url').value = url;
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
</script>
@endpush

@push('css')
<style>
    .select2-selection__rendered {
        line-height: 27px !important;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
    }

    .select2-selection__arrow {
        height: 34px !important;
    }
    
    #form_self label,
    #form_self input,
    #form_self select,
    #form_self button {
        font-weight: normal !important;
    }
</style>
@endpush