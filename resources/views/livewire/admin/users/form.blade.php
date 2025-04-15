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
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        </div>
    @stop
    
    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_active">Ativo</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_active" wire:model="use_active">
                                <label class="custom-control-label" for="use_active">
                                    {{ $use_active ? 'SIM' : 'NÃO' }}
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="use_cod_func">Código do Funcionário</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('use_cod_func') is-invalid @enderror" 
                                       id="use_cod_func" wire:model.lazy="use_cod_func" 
                                       placeholder="Código do Funcionário">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="buscarFuncionario">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                                @error('use_cod_func')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="use_name">Nome</label>
                            <input type="text" class="form-control @error('use_name') is-invalid @enderror" 
                                   id="use_name" wire:model.debounce.500ms="use_name" 
                                   placeholder="Nome do Usuário" required>
                            @error('use_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_username">Usuário</label>
                            <input type="text" class="form-control @error('use_username') is-invalid @enderror" 
                                   id="use_username" wire:model.lazy="use_username" 
                                   placeholder="Nome de Usuário" readonly>
                            @error('use_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_rol_id">Função</label>
                            <select class="form-control @error('use_rol_id') is-invalid @enderror" 
                                    id="use_rol_id" wire:model="use_rol_id" required>
                                <option value="">Selecione a Função</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->rol_id }}">
                                        {{ $role->rol_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('use_rol_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="use_email">Email (Opcional)</label>
                            <input type="email" class="form-control @error('use_email') is-invalid @enderror" 
                                   id="use_email" wire:model.lazy="use_email" 
                                   placeholder="Email do Usuário">
                            @error('use_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="use_cell">Telefone (Opcional)</label>
                            <input type="text" class="form-control @error('use_cell') is-invalid @enderror" 
                                   id="use_cell" wire:model.lazy="use_cell" 
                                   placeholder="(00) 00000-0000">
                            @error('use_cell')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_password">{{ $isEdit ? 'Nova Senha (Opcional)' : 'Senha' }}</label>
                            <input type="password" class="form-control @error('use_password') is-invalid @enderror" 
                                   id="use_password" wire:model.lazy="use_password" 
                                   placeholder="{{ $isEdit ? 'Deixe em branco para manter' : 'Digite a senha' }}">
                            @error('use_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Gerenciamento de Unidades -->
                <h4 class="mt-4">Gerenciamento de Unidades</h4>
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
                                                            class="btn btn-xs btn-success btn-add-unidade">
                                                        <i class="fas fa-plus"></i>
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
                                <h3 class="card-title">Unidades Associadas</h3>
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
                                                            class="btn btn-xs btn-danger btn-remove-unidade">
                                                        <i class="fas fa-trash"></i>
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
        document.addEventListener('livewire:load', function () {
            window.addEventListener('toastr:success', event => {
                toastr.success(event.detail.message);
            });

            window.addEventListener('toastr:error', event => {
                toastr.error(event.detail.message);
            });
            
            window.addEventListener('toastr:warning', event => {
                toastr.warning(event.detail.message);
            });
            
            window.addEventListener('toastr:info', event => {
                toastr.info(event.detail.message);
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            
            const nomeInput = document.getElementById('use_name');
            if (nomeInput) {
                // Usamos um debounce para evitar chamadas excessivas
                let debounceTimeout;
                
                nomeInput.addEventListener('input', function() {
                    // Cancelar timeout anterior se existir
                    if (debounceTimeout) {
                        clearTimeout(debounceTimeout);
                    }
                    
                    // Só executa se não estiver em modo de edição
                    if (!@this.isEdit) {
                        // Definir um novo timeout
                        debounceTimeout = setTimeout(() => {
                            // Não precisamos chamar @this.set('use_name', nome) aqui
                            // porque o wire:model.debounce.500ms já faz isso
                            
                            // Chamar o método para gerar username após 600ms
                            @this.call('gerarUsernameAutomatico');
                        }, 600); // Um pouco mais que o debounce do wire:model para garantir que o valor já foi atualizado
                    }
                });
            }

            // Máscara para código do funcionário
            const codigoInput = document.getElementById('use_cod_func');
    
            if (codigoInput) {
                // Usar um flag para evitar chamadas recursivas
                let isProcessing = false;
                
                codigoInput.addEventListener('input', function(e) {
                    // Evitar processamento recursivo
                    if (isProcessing) return;
                    isProcessing = true;
                    
                    // Obter a posição do cursor antes da alteração
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    
                    // Remove qualquer caractere que não seja número
                    let value = e.target.value.replace(/\D/g, '');
                    
                    // Limita a 10 dígitos
                    if (value.length > 10) {
                        value = value.substring(0, 10);
                    }
                    
                    // Atualiza o valor do campo sem recursão
                    e.target.value = value;
                    
                    // Atualiza o modelo do Livewire de forma debounced
                    window.livewire.find(@this.id).set('use_cod_func', value);
                    
                    // Restaurar a posição do cursor
                    this.setSelectionRange(start, end);
                    
                    isProcessing = false;
                });
            }

            // Buscar funcionário
            const buscarBtn = document.getElementById('buscarFuncionario');
            if (buscarBtn) {
                buscarBtn.addEventListener('click', function() {
                    const codigoFuncionario = document.getElementById('use_cod_func').value;
                    
                    if (!codigoFuncionario) {
                        Swal.fire({
                            title: 'Erro',
                            text: 'Por favor, insira um código de funcionário',
                            icon: 'warning'
                        });
                        return;
                    }

                    // Chamar método do Livewire para buscar funcionário
                    @this.call('buscarFuncionario', codigoFuncionario);
                });
            }

            // Filtro de unidades
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

            // Adicionar listeners de pesquisa
            document.getElementById('searchUnidadesDisponiveis').addEventListener('keyup', () => filterTable('searchUnidadesDisponiveis', 'unidadesDisponiveisTable'));
            document.getElementById('searchUnidadesAssociadas').addEventListener('keyup', () => filterTable('searchUnidadesAssociadas', 'unidadesAssociadasTable'));

            // Adicionar todos os usuários
            document.getElementById('addAllUnidades').addEventListener('click', function() {
                const rows = document.querySelectorAll('#unidadesDisponiveisTable tr');
                rows.forEach(row => {
                    const unidadeId = row.getAttribute('data-unidade-id');
                    @this.adicionarUnidade(unidadeId);
                });
            });

            // Remover todos os usuários
            document.getElementById('removeAllUnidades').addEventListener('click', function() {
                const rows = document.querySelectorAll('#unidadesAssociadasTable tr');
                rows.forEach(row => {
                    const unidadeId = row.getAttribute('data-unidade-id');
                    @this.removerUnidade(unidadeId);
                });
            });
        });
    </script>
    @stop
</div>