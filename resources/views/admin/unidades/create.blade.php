@extends('adminlte::page')
@include('components.alert.sweet-alert')
@section('title', 'Nova Unidade')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Nova Unidade</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('unidades.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nova Unidade</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('unidades.store') }}" method="POST" id="formUnidade">
                @csrf
                
                <!-- Campos ocultos para armazenar a lista de usuários -->
                <input type="hidden" name="usuarios_vincular" id="usuarios_vincular" value="">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_descricao">Nome</label>
                            <input type="text" class="form-control @error('uni_descricao') is-invalid @enderror" id="uni_descricao" name="uni_descricao" value="{{ old('uni_descricao') }}" required>
                            @error('uni_descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uni_codigo">Código</label>
                            <input type="text" class="form-control @error('uni_codigo') is-invalid @enderror" id="uni_codigo" name="uni_codigo" value="{{ old('uni_codigo') }}" required>
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
                            <input type="text" class="form-control @error('uni_cidade') is-invalid @enderror" id="uni_cidade" name="uni_cidade" value="{{ old('uni_cidade') }}" required>
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
                                <option value="AC" {{ old('uni_uf') == 'AC' ? 'selected' : '' }}>AC</option>
                                <option value="AL" {{ old('uni_uf') == 'AL' ? 'selected' : '' }}>AL</option>
                                <option value="AM" {{ old('uni_uf') == 'AM' ? 'selected' : '' }}>AM</option>
                                <option value="AP" {{ old('uni_uf') == 'AP' ? 'selected' : '' }}>AP</option>
                                <option value="BA" {{ old('uni_uf') == 'BA' ? 'selected' : '' }}>BA</option>
                                <option value="CE" {{ old('uni_uf') == 'CE' ? 'selected' : '' }}>CE</option>
                                <option value="DF" {{ old('uni_uf') == 'DF' ? 'selected' : '' }}>DF</option>
                                <option value="ES" {{ old('uni_uf') == 'ES' ? 'selected' : '' }}>ES</option>
                                <option value="GO" {{ old('uni_uf') == 'GO' ? 'selected' : '' }}>GO</option>
                                <option value="MA" {{ old('uni_uf') == 'MA' ? 'selected' : '' }}>MA</option>
                                <option value="MG" {{ old('uni_uf') == 'MG' ? 'selected' : '' }}>MG</option>
                                <option value="MS" {{ old('uni_uf') == 'MS' ? 'selected' : '' }}>MS</option>
                                <option value="MT" {{ old('uni_uf') == 'MT' ? 'selected' : '' }}>MT</option>
                                <option value="PA" {{ old('uni_uf') == 'PA' ? 'selected' : '' }}>PA</option>
                                <option value="PB" {{ old('uni_uf') == 'PB' ? 'selected' : '' }}>PB</option>
                                <option value="PE" {{ old('uni_uf') == 'PE' ? 'selected' : '' }}>PE</option>
                                <option value="PI" {{ old('uni_uf') == 'PI' ? 'selected' : '' }}>PI</option>
                                <option value="PR" {{ old('uni_uf') == 'PR' ? 'selected' : '' }}>PR</option>
                                <option value="RJ" {{ old('uni_uf') == 'RJ' ? 'selected' : '' }}>RJ</option>
                                <option value="RN" {{ old('uni_uf') == 'RN' ? 'selected' : '' }}>RN</option>
                                <option value="RO" {{ old('uni_uf') == 'RO' ? 'selected' : '' }}>RO</option>
                                <option value="RR" {{ old('uni_uf') == 'RR' ? 'selected' : '' }}>RR</option>
                                <option value="RS" {{ old('uni_uf') == 'RS' ? 'selected' : '' }}>RS</option>
                                <option value="SC" {{ old('uni_uf') == 'SC' ? 'selected' : '' }}>SC</option>
                                <option value="SE" {{ old('uni_uf') == 'SE' ? 'selected' : '' }}>SE</option>
                                <option value="SP" {{ old('uni_uf') == 'SP' ? 'selected' : '' }}>SP</option>
                                <option value="TO" {{ old('uni_uf') == 'TO' ? 'selected' : '' }}>TO</option>
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
                    <button type="button" class="btn btn-success" id="btnSalvar">
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
                                    @foreach($todosUsuarios ?? [] as $user)
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
                                    <!-- Tabela de usuários selecionados inicialmente vazia -->
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
        </div>
    </div>
@stop

@section('css')
    <style>
        .btn-xs {
            padding: .125rem .25rem;
            font-size: .75rem;
            line-height: 1.5;
            border-radius: .15rem;
        }
        .btn-success {
            background-color: #007bff;
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
        var unidadesIndexUrl = "{{ route('unidades.index') }}";
        
        // Variável global para tracking de usuários que vamos adicionar ou remover da nossa unidade
        var usuariosSelecionados = [];
        
        document.addEventListener('DOMContentLoaded', function() {
            // Pesquisa para os Usuários Disponíveis
            document.getElementById('searchUsersDisponiveis').addEventListener('keyup', function() {
                filterTable('searchUsersDisponiveis', 'usersDisponiveisTable');
            });
            
            // Pesquisa para os Usuários Selecionados
            document.getElementById('searchUsersSelecionados').addEventListener('keyup', function() {
                filterTable('searchUsersSelecionados', 'usersSelecionadosTable');
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
            
            // Adicionar usuário localmente
            document.querySelectorAll('.btn-add-user').forEach(function(button) {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    
                    addUserLocally(userId, userName);
                });
            });
            
            // Adicionar todos os usuários localmente
            document.getElementById('addAllUsers').addEventListener('click', function() {
                const users = document.querySelectorAll('#usersDisponiveisTable tr');
                if (users.length === 0) {
                    mostrarAlerta('Não há usuários disponíveis para adicionar.', 'erro');
                    return;
                }
                users.forEach(function(row) {
                    const userId = row.getAttribute('data-user-id');
                    const userName = row.querySelector('td').textContent.trim();
                    
                    addUserLocally(userId, userName);
                });
            });
            
            // Remover todos os usuários localmente
            document.getElementById('removeAllUsers').addEventListener('click', function() {
                const users = document.querySelectorAll('#usersSelecionadosTable tr');
                if (users.length === 0) {
                    mostrarAlerta('Não há usuários selecionados para remover.', 'erro');
                    return;
                }
                
                users.forEach(function(row) {
                    const userId = row.getAttribute('data-user-id');
                    const userName = row.querySelector('td').textContent.trim();
                    
                    removeUserLocally(userId, userName);
                });
            });
            
            // Evento para o botão salvar
            document.getElementById('btnSalvar').addEventListener('click', function() {
                // Preencher o campo oculto com a lista de usuários
                document.getElementById('usuarios_vincular').value = JSON.stringify(usuariosSelecionados);
                
                // Submeter o formulário com os dados da unidade e lista de usuários
                const form = document.getElementById('formUnidade');
                const formData = new FormData(form);
                
                // Mostrar loader ou desabilitar o botão
                const btnSalvar = document.getElementById('btnSalvar');
                const originalText = btnSalvar.innerHTML;
                btnSalvar.disabled = true;
                btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
                
                // Enviar a requisição pro back
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirecionar após sucesso
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Unidade criada com sucesso!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.href = data.redirect || unidadesIndexUrl;
                        });
                    } else {
                        // Restaurar o botão
                        btnSalvar.disabled = false;
                        btnSalvar.innerHTML = originalText;
                        
                        // Exibir erros do formulário
                        if (data.errors) {
                            let errorMessage = 'Erros de validação:\n';
                            for (const field in data.errors) {
                                errorMessage += `- ${data.errors[field].join('\n- ')}\n`;
                            }
                            mostrarAlerta(errorMessage, 'erro');
                        } else {
                            mostrarAlerta('Erro ao criar unidade: ' + (data.message || 'Erro desconhecido'), 'erro');
                        }
                    }
                })
                .catch(error => {
                    // Restaurar o botão
                    btnSalvar.disabled = false;
                    btnSalvar.innerHTML = originalText;
                    
                    console.error('Erro:', error);
                    mostrarAlerta('Erro ao processar a requisição.', 'erro');
                });
            });
            
            const codigoInput = document.getElementById('uni_codigo');
            if(codigoInput) {
                codigoInput.addEventListener('input', function(e) {
                    // Remove qualquer caractere que não seja número
                    let value = e.target.value.replace(/\D/g, '');
                    
                    // Limita a 3 dígitos (004, 001, coisas assim, geralmente é --)
                    if (value.length > 3) {
                        value = value.substring(0, 3);
                    }
                    // Atualiza o valor do campo
                    e.target.value = value;
                });
            }
        });
        
        // Funções para manipular os usuários localmente
        function addUserLocally(userId, userName) {
            // Verificar se o usuário já está na lista de selecionados
            if (usuariosSelecionados.includes(userId)) {
                return;
            }
            
            // Adicionar à lista de selecionados
            usuariosSelecionados.push(userId);
            
            // Mover o usuário para a tabela de selecionados (apenas visualmente)
            const row = document.querySelector(`#usersDisponiveisTable tr[data-user-id="${userId}"]`);
            if (row) {
                // Criar a nova linha para a tabela de selecionados
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
                    removeUserLocally(userId, userName);
                });
                
                // Adicionar à tabela de selecionados
                document.getElementById('usersSelecionadosTable').appendChild(newRow);
                
                // Remover da tabela de disponíveis
                row.remove();
            }
            
            console.log('Usuários selecionados:', usuariosSelecionados);
        }
        
        function removeUserLocally(userId, userName) {
            // Remover da lista de selecionados
            const index = usuariosSelecionados.indexOf(userId);
            if (index !== -1) {
                usuariosSelecionados.splice(index, 1);
            }
            
            // Mover o usuário para a tabela de disponíveis visaulmente falando
            const row = document.querySelector(`#usersSelecionadosTable tr[data-user-id="${userId}"]`);
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
                newRow.querySelector('.btn-add-user').addEventListener('click', function() {
                    addUserLocally(userId, userName);
                });
                document.getElementById('usersDisponiveisTable').appendChild(newRow);
                row.remove();
            }
            
            console.log('Usuários selecionados:', usuariosSelecionados);
        }
    </script>
@stop