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
                    <li class="breadcrumb-item"><a href="{{ route('unidades.index') }}">Unidades</a></li>
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
                            <label for="uni_codigo">Código</label>
                            <input type="text" class="form-control @error('uni_codigo') is-invalid @enderror" 
                                   id="uni_codigo" wire:model.lazy="uni_codigo" 
                                   placeholder="Digite o código da unidade" required>
                            @error('uni_codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_descricao">Nome</label>
                            <input type="text" class="form-control @error('uni_descricao') is-invalid @enderror" 
                                   id="uni_descricao" wire:model.lazy="uni_descricao" 
                                   placeholder="Digite o nome da unidade" required>
                            @error('uni_descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_cidade">Cidade</label>
                            <input type="text" class="form-control @error('uni_cidade') is-invalid @enderror" 
                                   id="uni_cidade" wire:model.lazy="uni_cidade" 
                                   placeholder="Digite a cidade" required>
                            @error('uni_cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_uf">UF</label>
                            <select class="form-control @error('uni_uf') is-invalid @enderror" 
                                    id="uni_uf" wire:model="uni_uf" required>
                                <option value="">Selecione a UF</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AM">AM</option>
                                <option value="AP">AP</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MG">MG</option>
                                <option value="MS">MS</option>
                                <option value="MT">MT</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="PR">PR</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="RS">RS</option>
                                <option value="SC">SC</option>
                                <option value="SE">SE</option>
                                <option value="SP">SP</option>
                                <option value="TO">TO</option>
                            </select>
                            @error('uni_uf')
                                <div class="invalid-feedback">{{ $message }}</div>
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
                                        <input type="text" id="searchUsersDisponiveis" class="form-control float-right" placeholder="Pesquisar">
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
                                                            class="btn btn-xs btn-success btn-add-user">
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
                                <button type="button" class="btn btn-sm btn-primary" id="addAllUsers">
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
                                        <input type="text" id="searchUsersSelecionados" class="form-control float-right" placeholder="Pesquisar">
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
                                                            class="btn btn-xs btn-danger btn-remove-user">
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
                                <button type="button" class="btn btn-sm btn-danger" id="removeAllUsers">
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
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Lista de SelfCheckouts</h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <input type="text" id="searchSelfsAssociados" class="form-control float-right" placeholder="Pesquisar">
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
                                        @if($isEdit)
                                            @foreach($selfsAssociados as $self)
                                            <tr>
                                                <td>{{ $self->sel_id }}</td>
                                                <td>{{ $self->sel_name }}</td>
                                                <td>{{ $self->sel_pdv_ip }}</td>
                                                <td>{{ $self->sel_rtsp_url }}</td>
                                                <td>
                                                    @if($self->sel_status)
                                                        <span class="badge badge-success">Ativo</span>
                                                    @else
                                                        <span class="badge badge-danger">Inativo</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
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
                                wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save"></i> Salvar
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin"></i> Salvando...
                            </span>
                        </button>
                        <a href="{{ route('unidades.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const codigoInput = document.getElementById('uni_codigo');
    
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
                    
                    // Limita a 3 dígitos
                    if (value.length > 3) {
                        value = value.substring(0, 3);
                    }
                    
                    // Atualiza o valor do campo sem recursão
                    e.target.value = value;
                    
                    // Atualiza o modelo do Livewire de forma segura
                    window.livewire.find(@this.id).set('uni_codigo', value);
                    
                    // Restaurar a posição do cursor
                    this.setSelectionRange(start, end);
                    
                    isProcessing = false;
                });
    }

            document.getElementById('addAllUsers').addEventListener('click', function() {
                const rows = document.querySelectorAll('#usersDisponiveisTable tr');
                rows.forEach(row => {
                    const userId = row.getAttribute('data-user-id');
                    @this.adicionarUsuario(userId);
                });
            });

            document.getElementById('removeAllUsers').addEventListener('click', function() {
                const rows = document.querySelectorAll('#usersSelecionadosTable tr');
                rows.forEach(row => {
                    const userId = row.getAttribute('data-user-id');
                    @this.removerUsuario(userId);
                });
            });

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

            document.getElementById('searchUsersDisponiveis').addEventListener('keyup', () => filterTable('searchUsersDisponiveis', 'usersDisponiveisTable'));
            document.getElementById('searchUsersSelecionados').addEventListener('keyup', () => filterTable('searchUsersSelecionados', 'usersSelecionadosTable'));
            
            @if($isEdit)
            document.getElementById('searchSelfsAssociados').addEventListener('keyup', () => filterTable('searchSelfsAssociados','selfsAssociadosTable'));
            @endif
        });
    </script>
    @stop
</div>