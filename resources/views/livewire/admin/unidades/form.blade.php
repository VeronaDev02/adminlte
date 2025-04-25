@section('title', $title)
    
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('unidades.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active" style="font-weight: normal;">{{ $isEdit ? 'Editar' : 'Criar' }} Unidade</li>
            </ol>
        </div>
    </div>
@stop

<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="uni_codigo">Código</label>
                            <input type="text" 
                                   class="form-control @error('uni_codigo') is-invalid @enderror" 
                                   id="uni_codigo" 
                                   wire:model.lazy="uni_codigo" 
                                   placeholder="Digite o código da unidade" 
                                   required>
                            @error('uni_codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="uni_nome">Nome da Unidade</label>
                            <input type="text" 
                                   class="form-control @error('uni_nome') is-invalid @enderror" 
                                   id="uni_nome" 
                                   wire:model.lazy="uni_nome" 
                                   placeholder="Digite o nome da unidade" 
                                   required>
                            @error('uni_nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="uni_tip_id">Tipo de Unidade</label>
                            <div wire:ignore>
                                <select class="form-control select2 @error('uni_tip_id') is-invalid @enderror" 
                                        id="uni_tip_id" 
                                        required>
                                    <option value="">Selecione um tipo</option>
                                    @foreach($tiposUnidade as $tipo)
                                        <option value="{{ $tipo->tip_id }}" 
                                                {{ $uni_tip_id == $tipo->tip_id ? 'selected' : '' }}>
                                            {{ $tipo->tip_nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('uni_tip_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <h4 class="mt-4">Gerenciamento de Usuários</h4>
                <div class="row">
                    <div class="col-md-5">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Usuários Disponíveis</h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <input type="text" 
                                            id="searchUsersDisponiveis" 
                                            class="form-control float-right" 
                                            placeholder="Pesquisar">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped table-bordered">
                                    <tbody id="usersDisponiveisTable">
                                        @foreach($usuarios as $usuario)
                                            @if(!in_array($usuario->use_id, $usuariosSelecionados))
                                            <tr data-user-id="{{ $usuario->use_id }}">
                                                <td>{{ $usuario->use_name }}</td>
                                                <td class="text-right" style="width: 80px;">
                                                <button type="button" 
                                                        wire:click="adicionarUsuario({{ $usuario->use_id }})" 
                                                        class="btn btn-xs btn-default text-primary mx-1 shadow">
                                                    <i class="fa fa-lg fa-fw fa-plus"></i>
                                                </button>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-center">
                                <button type="button" 
                                        class="btn btn-sm btn-primary" 
                                        wire:click="adicionarTodosUsuarios"
                                        data-toggle="tooltip"
                                        title="Adicionar Todos Usuários">
                                    <i class="fas fa-angle-double-right"></i> Adicionar Todos
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <i class="fas fa-exchange-alt fa-2x text-muted mb-2"></i>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Usuários Selecionados</h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <input type="text" 
                                            id="searchUsersSelecionados" 
                                            class="form-control float-right" 
                                            placeholder="Pesquisar">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped table-bordered">
                                    <tbody id="usersSelecionadosTable">
                                        @foreach($usuarios as $usuario)
                                            @if(in_array($usuario->use_id, $usuariosSelecionados))
                                            <tr data-user-id="{{ $usuario->use_id }}">
                                                <td>{{ $usuario->use_name }}</td>
                                                <td class="text-right" style="width: 80px;">
                                                    <button type="button" 
                                                            wire:click="removerUsuario({{ $usuario->use_id }})" 
                                                            class="btn btn-xs btn-default text-danger mx-1 shadow">
                                                        <i class="fa fa-lg fa-fw fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-center">
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        wire:click="removerTodosUsuarios"
                                        data-toggle="tooltip"
                                        title="Remover Todos Usuários">
                                    <i class="fas fa-angle-double-left"></i> Remover Todos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if($isEdit)
                <h4 class="mt-4">SelfCheckouts Associados</h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Lista de SelfCheckouts</h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <input type="text" 
                                               id="searchSelfsAssociados" 
                                               class="form-control float-right" 
                                               placeholder="Pesquisar">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nome</th>
                                            <th>IP PDV</th>
                                            <th>URL RTSP</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selfsAssociadosTable">
                                        @foreach($selfsAssociados as $self)
                                        <tr>
                                            <td>{{ $self->sel_id }}</td>
                                            <td>{{ $self->sel_name }}</td>
                                            <td>{{ $self->sel_pdv_ip }}</td>
                                            <td>{{ $self->sel_rtsp_path }}</td>
                                            <td>
                                                @if($self->sel_status)
                                                    <span class="badge badge-success">Ativo</span>
                                                @else
                                                    <span class="badge badge-danger">Inativo</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" 
                                class="btn btn-primary" 
                                wire:loading.attr="disabled" 
                                wire:target="save"
                                data-toggle="tooltip"
                                title="Salvar Unidade">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save"></i> Salvar
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin"></i> Salvando...
                            </span>
                        </button>
                        <a href="{{ route('unidades.index') }}" 
                           class="btn btn-secondary ml-2"
                           data-toggle="tooltip"
                           title="Voltar para Lista de Unidades">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </form>
        </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    var select2Initialized = false;
    
    function initSelect2() {
        if (select2Initialized) {
            return;
        }
        
        $('#uni_tip_id').select2({
            placeholder: 'Selecione um tipo',
            allowClear: true
        });
        
        $('#uni_tip_id').on('change', function() {
            @this.set('uni_tip_id', $(this).val());
        });
        
        select2Initialized = true;
    }
    
    function initTooltips() {
        try {
            if (!$('[data-toggle="tooltip"]').hasClass('tooltipstered')) {
                $('[data-toggle="tooltip"]').tooltip({
                    trigger: 'hover'
                });
            }
        } catch (error) {
            console.error('Erro ao inicializar tooltips:', error);
        }
    }
    
    $(document).ready(function() {
        initSelect2();
        initTooltips();
    });
    
    function filterTable(inputId, tableId) {
        const input = document.getElementById(inputId);
        if (!input) {
            return;
        }
        
        const filter = input.value.toUpperCase();
        const rows = document.querySelectorAll(`#${tableId} tr`);
        
        rows.forEach(row => {
            const allCells = row.querySelectorAll('td');
            if (allCells.length > 0) {
                let found = false;
                allCells.forEach(cell => {
                    const txtValue = cell.textContent || cell.innerText;
                    if (txtValue.toUpperCase().includes(filter)) {
                        found = true;
                    }
                });
                row.style.display = found ? '' : 'none';
            }
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const searchUsersDisponiveis = document.getElementById('searchUsersDisponiveis');
        if (searchUsersDisponiveis) {
            searchUsersDisponiveis.addEventListener('keyup', () => filterTable('searchUsersDisponiveis', 'usersDisponiveisTable'));
        }
        
        const searchUsersSelecionados = document.getElementById('searchUsersSelecionados');
        if (searchUsersSelecionados) {
            searchUsersSelecionados.addEventListener('keyup', () => filterTable('searchUsersSelecionados', 'usersSelecionadosTable'));
        }
        
        const searchSelfsAssociados = document.getElementById('searchSelfsAssociados');
        if (searchSelfsAssociados) {
            searchSelfsAssociados.addEventListener('keyup', () => filterTable('searchSelfsAssociados', 'selfsAssociadosTable'));
        }
    });
    
    if (window.Livewire) {
        document.addEventListener('livewire:load', function () {
            initTooltips();
        });
    }
</script>
@stop