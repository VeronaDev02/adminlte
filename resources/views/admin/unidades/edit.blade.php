@extends('adminlte::page')

@section('title', 'Editar Unidade')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Editar Unidade</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('unidades.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Unidade</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('unidades.update', $unidade->uni_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_descricao">Nome</label>
                            <input type="text" class="form-control @error('uni_descricao') is-invalid @enderror" id="uni_descricao" name="uni_descricao" value="{{ old('uni_descricao', $unidade->uni_descricao) }}" required>
                            @error('uni_descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_codigo">Código</label>
                            <input type="text" class="form-control @error('uni_codigo') is-invalid @enderror" id="uni_codigo" name="uni_codigo" value="{{ old('uni_codigo', $unidade->uni_codigo) }}" required>
                            @error('uni_codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_cidade">Cidade</label>
                            <input type="text" class="form-control @error('uni_cidade') is-invalid @enderror" id="uni_cidade" name="uni_cidade" value="{{ old('uni_cidade', $unidade->uni_cidade) }}" required>
                            @error('uni_cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_uf">UF</label>
                            <select class="form-control @error('uni_uf') is-invalid @enderror" id="uni_uf" name="uni_uf" required>
                                <option value="">Selecione...</option>
                                <option value="AC" {{ (old('uni_uf', $unidade->uni_uf) == 'AC') ? 'selected' : '' }}>AC</option>
                                <option value="AL" {{ (old('uni_uf', $unidade->uni_uf) == 'AL') ? 'selected' : '' }}>AL</option>
                                <option value="AM" {{ (old('uni_uf', $unidade->uni_uf) == 'AM') ? 'selected' : '' }}>AM</option>
                                <option value="AP" {{ (old('uni_uf', $unidade->uni_uf) == 'AP') ? 'selected' : '' }}>AP</option>
                                <option value="BA" {{ (old('uni_uf', $unidade->uni_uf) == 'BA') ? 'selected' : '' }}>BA</option>
                                <option value="CE" {{ (old('uni_uf', $unidade->uni_uf) == 'CE') ? 'selected' : '' }}>CE</option>
                                <option value="DF" {{ (old('uni_uf', $unidade->uni_uf) == 'DF') ? 'selected' : '' }}>DF</option>
                                <option value="ES" {{ (old('uni_uf', $unidade->uni_uf) == 'ES') ? 'selected' : '' }}>ES</option>
                                <option value="GO" {{ (old('uni_uf', $unidade->uni_uf) == 'GO') ? 'selected' : '' }}>GO</option>
                                <option value="MA" {{ (old('uni_uf', $unidade->uni_uf) == 'MA') ? 'selected' : '' }}>MA</option>
                                <option value="MG" {{ (old('uni_uf', $unidade->uni_uf) == 'MG') ? 'selected' : '' }}>MG</option>
                                <option value="MS" {{ (old('uni_uf', $unidade->uni_uf) == 'MS') ? 'selected' : '' }}>MS</option>
                                <option value="MT" {{ (old('uni_uf', $unidade->uni_uf) == 'MT') ? 'selected' : '' }}>MT</option>
                                <option value="PA" {{ (old('uni_uf', $unidade->uni_uf) == 'PA') ? 'selected' : '' }}>PA</option>
                                <option value="PB" {{ (old('uni_uf', $unidade->uni_uf) == 'PB') ? 'selected' : '' }}>PB</option>
                                <option value="PE" {{ (old('uni_uf', $unidade->uni_uf) == 'PE') ? 'selected' : '' }}>PE</option>
                                <option value="PI" {{ (old('uni_uf', $unidade->uni_uf) == 'PI') ? 'selected' : '' }}>PI</option>
                                <option value="PR" {{ (old('uni_uf', $unidade->uni_uf) == 'PR') ? 'selected' : '' }}>PR</option>
                                <option value="RJ" {{ (old('uni_uf', $unidade->uni_uf) == 'RJ') ? 'selected' : '' }}>RJ</option>
                                <option value="RN" {{ (old('uni_uf', $unidade->uni_uf) == 'RN') ? 'selected' : '' }}>RN</option>
                                <option value="RO" {{ (old('uni_uf', $unidade->uni_uf) == 'RO') ? 'selected' : '' }}>RO</option>
                                <option value="RR" {{ (old('uni_uf', $unidade->uni_uf) == 'RR') ? 'selected' : '' }}>RR</option>
                                <option value="RS" {{ (old('uni_uf', $unidade->uni_uf) == 'RS') ? 'selected' : '' }}>RS</option>
                                <option value="SC" {{ (old('uni_uf', $unidade->uni_uf) == 'SC') ? 'selected' : '' }}>SC</option>
                                <option value="SE" {{ (old('uni_uf', $unidade->uni_uf) == 'SE') ? 'selected' : '' }}>SE</option>
                                <option value="SP" {{ (old('uni_uf', $unidade->uni_uf) == 'SP') ? 'selected' : '' }}>SP</option>
                                <option value="TO" {{ (old('uni_uf', $unidade->uni_uf) == 'TO') ? 'selected' : '' }}>TO</option>
                            </select>
                            @error('uni_uf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-3 mb-4">
                    <a href="{{ route('unidades.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
            
            <!-- Gerenciamento de Usuários -->
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
                                    @foreach($usuariosDisponiveis ?? [] as $user)
                                    <tr data-user-id="{{ $user->use_id }}">
                                        <td>{{ $user->use_name }}</td>
                                        <td class="text-right" style="width: 80px;">
                                            <button type="button" class="btn btn-xs btn-success btn-add-user" data-user-id="{{ $user->use_id }}" data-user-name="{{ $user->use_name }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
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
                            <h3 class="card-title">Usuários Associados</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" id="searchUsersAssociados" class="form-control float-right" placeholder="Pesquisar">
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
                                <tbody id="usersAssociadosTable">
                                    @php
                                        // Buscar usuários associados à unidade através da tabela units
                                        $usuariosAssociados = \App\Models\Unit::where('unit_uni_id', $unidade->uni_id)
                                            ->join('users', 'users.use_id', '=', 'units.unit_use_id')
                                            ->select('users.*')
                                            ->get();
                                    @endphp
                                    
                                    @foreach($usuariosAssociados as $user)
                                    <tr data-user-id="{{ $user->use_id }}">
                                        <td>{{ $user->use_name }}</td>
                                        <td class="text-right" style="width: 80px;">
                                            <button type="button" class="btn btn-xs btn-danger btn-remove-user" data-user-id="{{ $user->use_id }}" data-user-name="{{ $user->use_name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
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
            
            <!-- SelfCheckouts Associados -->
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
                                    @foreach($unidade->selfs as $self)
                                    <tr data-self-id="{{ $self->sel_id }}">
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
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Forms ocultos para AJAX -->
    <form id="form-add-user" action="{{ route('unidades.add.usuario', $unidade->uni_id) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="user_id" id="add_user_id">
    </form>
    
    <form id="form-remove-user" action="{{ route('unidades.remove.usuario', $unidade->uni_id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="user_id" id="remove_user_id">
    </form>
@stop

@section('css')
    <style>
        .btn-xs {
            padding: .125rem .25rem;
            font-size: .75rem;
            line-height: 1.5;
            border-radius: .15rem;
        }
        .btn-xs i {
            font-size: 0.875rem;
        }
        .card-outline {
            border-top: 3px solid;
        }
        .card-outline.card-primary {
            border-top-color: #007bff;
        }
        .card-outline.card-success {
            border-top-color: #28a745;
        }
        .loading-indicator {
            display: none;
            text-align: center;
            padding: 15px;
        }
    </style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pesquisa para SelfCheckouts Associados
        document.getElementById('searchSelfsAssociados').addEventListener('keyup', function() {
            filterTable('searchSelfsAssociados', 'selfsAssociadosTable');
        });
        
        // Pesquisa para Usuários Disponíveis
        document.getElementById('searchUsersDisponiveis').addEventListener('keyup', function() {
            filterTable('searchUsersDisponiveis', 'usersDisponiveisTable');
        });
        
        // Pesquisa para Usuários Associados
        document.getElementById('searchUsersAssociados').addEventListener('keyup', function() {
            filterTable('searchUsersAssociados', 'usersAssociadosTable');
        });
        
        // Função de filtro para tabelas
        function filterTable(inputId, tableId) {
            var input = document.getElementById(inputId);
            var filter = input.value.toUpperCase();
            var table = document.getElementById(tableId);
            var tr = table.getElementsByTagName("tr");
            
            for (var i = 0; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    var txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
        
        // Adicionar usuário via AJAX
        document.querySelectorAll('.btn-add-user').forEach(function(button) {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                
                addUser(userId, userName);
            });
        });
        
        // Remover usuário via AJAX
        document.querySelectorAll('.btn-remove-user').forEach(function(button) {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                
                removeUser(userId, userName);
            });
        });
        
        // Adicionar todos os usuários
        document.getElementById('addAllUsers').addEventListener('click', function() {
            const users = document.querySelectorAll('#usersDisponiveisTable tr');
            if (users.length === 0) {
                alert('Não há usuários disponíveis para adicionar.');
                return;
            }
            users.forEach(function(row) {
                const userId = row.getAttribute('data-user-id');
                const userName = row.querySelector('td').textContent.trim();
                
                addUser(userId, userName, false); // false = não mostrar confirmação individual
            });
        });
        
        // Remover todos os usuários
        document.getElementById('removeAllUsers').addEventListener('click', function() {
            const users = document.querySelectorAll('#usersAssociadosTable tr');
            if (users.length === 0) {
                alert('Não há usuários associados para remover.');
                return;
            }
            
            users.forEach(function(row) {
                const userId = row.getAttribute('data-user-id');
                const userName = row.querySelector('td').textContent.trim();
                
                removeUser(userId, userName, false); // false = não mostrar confirmação individual
            });
        });
        
        // Funções para manipular usuários
        function addUser(userId, userName, showConfirmation = true) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('user_id', userId);
            
            fetch(`{{ route('unidades.add.usuario', $unidade->uni_id) }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mover o usuário para a tabela de associados
                    const row = document.querySelector(`#usersDisponiveisTable tr[data-user-id="${userId}"]`);
                    if (row) {
                        // Criar a nova linha para a tabela de associados
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-user-id', userId);
                        newRow.innerHTML = `
                            <td>${userName}</td>
                            <td class="text-right" style="width: 80px;">
                                <button type="button" class="btn btn-xs btn-danger btn-remove-user" data-user-id="${userId}" data-user-name="${userName}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        
                        // Adicionar evento ao novo botão
                        newRow.querySelector('.btn-remove-user').addEventListener('click', function() {
                            removeUser(userId, userName);
                        });
                        
                        // Adicionar à tabela de associados
                        document.getElementById('usersAssociadosTable').appendChild(newRow);
                        
                        // Remover da tabela de disponíveis
                        row.remove();
                    }
                } else {
                    alert('Erro ao adicionar usuário: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar a requisição.');
            });
        }
        
        function removeUser(userId, userName, showConfirmation = true) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('_method', 'DELETE');
            formData.append('user_id', userId);
            
            fetch(`{{ route('unidades.remove.usuario', $unidade->uni_id) }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mover o usuário para a tabela de disponíveis
                    const row = document.querySelector(`#usersAssociadosTable tr[data-user-id="${userId}"]`);
                    if (row) {
                        // Criar a nova linha para a tabela de disponíveis
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-user-id', userId);
                        newRow.innerHTML = `
                            <td>${userName}</td>
                            <td class="text-right" style="width: 80px;">
                                <button type="button" class="btn btn-xs btn-success btn-add-user" data-user-id="${userId}" data-user-name="${userName}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </td>
                        `;
                        
                        // Adicionar evento ao novo botão
                        newRow.querySelector('.btn-add-user').addEventListener('click', function() {
                            addUser(userId, userName);
                        });
                        
                        // Adicionar à tabela de disponíveis
                        document.getElementById('usersDisponiveisTable').appendChild(newRow);
                        
                        // Remover da tabela de associados
                        row.remove();
                    }
                } else {
                    alert('Erro ao remover usuário: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar a requisição.');
            });
        }
        
        
    });
</script>
@stop