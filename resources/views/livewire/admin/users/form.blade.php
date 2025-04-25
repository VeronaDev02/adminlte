<div>
    @section('title', $title)
    
    @section('content_header')
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('users.index') }}">Usuários</a></li>
                    <li class="breadcrumb-item active" style="font-weight: normal;">{{ $isEdit ? 'Editar' : 'Criar' }} Usuário</li>
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
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-1 form-group">
                        <label for="use_active">Ativo</label><br>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="use_active" wire:model="use_active">
                            <label class="custom-control-label" for="use_active">
                                {{ $use_active ? 'SIM' : 'NÃO' }}
                            </label>
                        </div>
                        @error('use_active')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="col-2 form-group">
                        <label for="use_cod_func">Código do Funcionário</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('use_cod_func') is-invalid @enderror" 
                                   id="use_cod_func" wire:model.lazy="use_cod_func" 
                                   placeholder="Código">
                            <div class="input-group-append">
                                <button class="btn btn-outline-info" id="buscarFuncionario" type="button">
                                    <i id="originalIcon" class="fas fa-check"></i>
                                    <i id="loadingIcon" class="fas fa-spinner fa-spin" style="display: none;"></i>
                                </button>
                            </div>
                            @error('use_cod_func')
                                <span class="invalid-feedback" style="display: unset;" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-2 form-group">
                        <label for="use_name">Nome</label>
                        <input type="text" class="form-control @error('use_name') is-invalid @enderror" 
                               onkeyup="gerarUsernameAutomatico()" onchange="gerarUsernameAutomatico()"
                               id="use_name" wire:model.debounce.500ms="use_name" 
                               placeholder="Nome">
                        @error('use_name')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="col-2 form-group">
                        <label for="use_username">Usuário</label>
                        <input class="form-control @error('use_username') is-invalid @enderror" 
                               readonly id="use_username" wire:model.lazy="use_username" 
                               type="text">
                        @error('use_username')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="col-2 form-group">
                        <label for="use_rol_id">Função</label>
                        <select name="use_rol_id" id="use_rol_id" 
                                class="form-control @error('use_rol_id') is-invalid @enderror"
                                wire:model="use_rol_id">
                            <option value="">Selecione a Função</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->rol_id }}">
                                    {{ $role->rol_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('use_rol_id')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="col-3 form-group">
                        <label for="use_email">Email (Opcional)</label>
                        <input class="form-control @error('use_email') is-invalid @enderror" 
                               id="use_email" wire:model.lazy="use_email" 
                               type="email" placeholder="email@example.com">
                        @error('use_email')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-2 form-group">
                        <label for="use_cell">Telefone (Opcional)</label>
                        <input class="form-control @error('use_cell') is-invalid @enderror" 
                               id="use_cell" wire:model.lazy="use_cell" 
                               type="text" placeholder="(00) 00000-0000">
                        @error('use_cell')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="col-2 form-group">
                        <label for="use_password">{{ $isEdit ? 'Nova Senha (Opcional)' : 'Senha' }}</label>
                        <input class="form-control @error('use_password') is-invalid @enderror" 
                               id="use_password" wire:model.lazy="use_password" 
                               type="password" placeholder="{{ $isEdit ? 'Deixe em branco para manter' : 'Digite a senha' }}">
                        @error('use_password')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="unidades">Unidades</label>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Unidades Disponíveis</h3>
                                    <div class="card-tools">
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="text" id="searchUnidadesDisponiveis" class="form-control float-right" placeholder="Pesquisar">
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
                                        <tbody id="unidadesDisponiveisTable">
                                            @foreach($unidades as $unidade)
                                                @if(!in_array($unidade->uni_id, $usuariosSelecionados))
                                                <tr data-unidade-id="{{ $unidade->uni_id }}">
                                                    <td>{{ $unidade->uni_codigo }} - {{ $unidade->nome }}</td>
                                                    <td class="text-right" style="width: 80px;">
                                                        <button type="button" 
                                                                wire:click="adicionarUnidade({{ $unidade->uni_id }})" 
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
                                    <button type="button" class="btn btn-sm btn-primary" id="addAllUnidades">
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
                                    <h3 class="card-title">Unidades Selecionadas</h3>
                                    <div class="card-tools">
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="text" id="searchUnidadesAssociadas" class="form-control float-right" placeholder="Pesquisar">
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
                                        <tbody id="unidadesAssociadasTable">
                                            @foreach($unidades as $unidade)
                                                @if(in_array($unidade->uni_id, $usuariosSelecionados))
                                                <tr data-unidade-id="{{ $unidade->uni_id }}">
                                                    <td>{{ $unidade->uni_codigo }} - {{ $unidade->nome }}</td>
                                                    <td class="text-right" style="width: 80px;">
                                                        <button type="button" 
                                                                wire:click="removerUnidade({{ $unidade->uni_id }})" 
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
                                    <button type="button" class="btn btn-sm btn-danger" id="removeAllUnidades">
                                        <i class="fas fa-angle-double-left"></i> Remover Todos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-2">
                    <div class="col-12">
                        <button id="enviar" class="btn bg-primary" type="submit">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save"></i> 
                                @if ($isEdit)
                                    Alterar
                                @else
                                    Cadastrar
                                @endif
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin"></i> Salvando...
                            </span>
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @section('js')
    <script>

        const codigoInput = document.getElementById('use_cod_func');
        codigoInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                buscarFuncionario();
            }
        });

        function buscarFuncionario() {
            const codigoFuncionario = document.getElementById('use_cod_func').value;
            
            if (!codigoFuncionario) {
                Swal.fire({
                    title: 'Erro',
                    text: 'Por favor, insira um código de funcionário',
                    icon: 'warning'
                });
                return;
            }
            
            $('#originalIcon').hide();
            $('#loadingIcon').show();

            @this.call('buscarFuncionario', codigoFuncionario).then(() => {
                $('#originalIcon').show();
                $('#loadingIcon').hide();
            });
        }

        function gerarUsernameAutomatico() {
            @this.call('gerarUsernameAutomatico');
        }
        
        document.addEventListener('livewire:load', function () {

            window.addEventListener('admin-toastr', event => {
                toastr[event.detail.type](event.detail.message);
            });

            const codigoInput = document.getElementById('use_cod_func');
            codigoInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    buscarFuncionario();
                }
            });
            
            const buscarBtn = document.getElementById('buscarFuncionario');
            if (buscarBtn) {
                buscarBtn.addEventListener('click', buscarFuncionario);
            }

            function filterTable(inputId, tableId) {
                const input = document.getElementById(inputId);
                const filter = input.value.toUpperCase();
                const rows = document.querySelectorAll(`#${tableId} tr`);
                
                rows.forEach(row => {
                    const td = row.querySelector('td');
                    if (td) {
                        const txtValue = td.textContent || td.innerText;
                        row.style.display = txtValue.toUpperCase().includes(filter) ? '' : 'none';
                    }
                });
            }

            document.getElementById('searchUnidadesDisponiveis').addEventListener('keyup', () => filterTable('searchUnidadesDisponiveis', 'unidadesDisponiveisTable'));
            document.getElementById('searchUnidadesAssociadas').addEventListener('keyup', () => filterTable('searchUnidadesAssociadas', 'unidadesAssociadasTable'));

            document.getElementById('addAllUnidades').addEventListener('click', function() {
                const rows = document.querySelectorAll('#unidadesDisponiveisTable tr:not([style*="display: none"])');
                if (rows.length > 0) {
                    rows.forEach(row => {
                        const unidadeId = row.getAttribute('data-unidade-id');
                        @this.adicionarUnidade(unidadeId);
                    });
                }
            });

            document.getElementById('removeAllUnidades').addEventListener('click', function() {
                const rows = document.querySelectorAll('#unidadesAssociadasTable tr:not([style*="display: none"])');
                if (rows.length > 0) {
                    rows.forEach(row => {
                        const unidadeId = row.getAttribute('data-unidade-id');
                        @this.removerUnidade(unidadeId);
                    });
                }
            });
            
            $(function() {
                $('#use_cell').inputmask("(99) 99999-9999");
            });
        });
    </script>
    @stop
    
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
    </style>
    @endpush
</div>