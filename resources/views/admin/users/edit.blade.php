@extends('adminlte::page')

@section('title', 'Editar Usuário')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Editar Usuário</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Usuário</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $user->use_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_name">Nome</label>
                            <input type="text" class="form-control @error('use_name') is-invalid @enderror" id="use_name" name="use_name" value="{{ old('use_name', $user->use_name) }}" required>
                            @error('use_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_username">Username</label>
                            <input type="text" class="form-control @error('use_username') is-invalid @enderror" id="use_username" name="use_username" value="{{ old('use_username', $user->use_username) }}" required>
                            @error('use_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_email">Email</label>
                            <input type="email" class="form-control @error('use_email') is-invalid @enderror" id="use_email" name="use_email" value="{{ old('use_email', $user->use_email) }}" required>
                            @error('use_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_password">Senha (deixe em branco para manter a atual)</label>
                            <input type="password" class="form-control @error('use_password') is-invalid @enderror" id="use_password" name="use_password">
                            @error('use_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_rol_id">Role (Função/Cargo)</label>
                            <select class="form-control @error('use_rol_id') is-invalid @enderror" id="use_rol_id" name="use_rol_id">
                                <option value="">Selecione...</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->rol_id }}" {{ (old('use_rol_id', $user->use_rol_id) == $role->rol_id) ? 'selected' : '' }}>
                                        {{ $role->rol_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('use_rol_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_cod_func">Código do Funcionário</label>
                            <input type="text" class="form-control @error('use_cod_func') is-invalid @enderror" id="use_cod_func" name="use_cod_func" value="{{ old('use_cod_func', $user->use_cod_func) }}">
                            @error('use_cod_func')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_active" name="use_active" value="1" {{ old('use_active', $user->use_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_active">Usuário Ativo</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_login_ativo" name="use_login_ativo" value="1" {{ old('use_login_ativo', $user->use_login_ativo) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_login_ativo">Login Ativo</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_allow_updates" name="use_allow_updates" value="1" {{ old('use_allow_updates', $user->use_allow_updates) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_allow_updates">Permitir Updates</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-3 mb-4">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
            
            <!-- Gerenciamento de Unidades -->
            <input type="hidden" id="unidadesModificadas" value="0">
            <h4 class="mt-4">Gerenciamento de Unidades</h4>
            <p class="text-muted mb-2">As alterações nas unidades serão salvas automaticamente.</p>
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
                                    @foreach($unidadesDisponiveis ?? [] as $unidade)
                                    <tr data-unidade-id="{{ $unidade->uni_id }}">
                                        <td>{{ $unidade->uni_codigo }} - {{ $unidade->uni_descricao }}</td>
                                        <td class="text-right" style="width: 80px;">
                                            <button type="button" class="btn btn-xs btn-success btn-add-unidade" data-unidade-id="{{ $unidade->uni_id }}" data-unidade-name="{{ $unidade->uni_codigo }} - {{ $unidade->uni_descricao }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-sm btn-primary" id="addAllUnidades">
                                <i class="fas fa-angle-double-right"></i> Adicionar Todas
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
                                    @php
                                        // Buscar unidades associadas ao usuário através da tabela units
                                        $unidadesAssociadas = \App\Models\Unit::where('unit_use_id', $user->use_id)
                                            ->join('unidade', 'unidade.uni_id', '=', 'units.unit_uni_id')
                                            ->select('unidade.*')
                                            ->get();
                                    @endphp
                                    
                                    @foreach($unidadesAssociadas as $unidade)
                                    <tr data-unidade-id="{{ $unidade->uni_id }}">
                                        <td>{{ $unidade->uni_codigo }} - {{ $unidade->uni_descricao }}</td>
                                        <td class="text-right" style="width: 80px;">
                                            <button type="button" class="btn btn-xs btn-danger btn-remove-unidade" data-unidade-id="{{ $unidade->uni_id }}" data-unidade-name="{{ $unidade->uni_codigo }} - {{ $unidade->uni_descricao }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-sm btn-danger" id="removeAllUnidades">
                                <i class="fas fa-angle-double-left"></i> Remover Todas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Forms ocultos para AJAX -->
    <form id="form-add-unidade" action="{{ route('users.add-unidade', $user->use_id) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="unidade_id" id="add_unidade_id">
    </form>
    
    <form id="form-remove-unidade" action="{{ route('users.remove-unidade', $user->use_id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="unidade_id" id="remove_unidade_id">
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
        .custom-switch .custom-control-label::before {
            height: 1.25rem;
        }
        .custom-control-input:checked~.custom-control-label::before {
            color: #fff;
            border-color: #28a745;
            background-color: #28a745;
        }
    </style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pesquisa para Unidades Disponíveis
        document.getElementById('searchUnidadesDisponiveis').addEventListener('keyup', function() {
            filterTable('searchUnidadesDisponiveis', 'unidadesDisponiveisTable');
        });
        
        // Pesquisa para Unidades Associadas
        document.getElementById('searchUnidadesAssociadas').addEventListener('keyup', function() {
            filterTable('searchUnidadesAssociadas', 'unidadesAssociadasTable');
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
        
        // Adicionar unidade via AJAX
        document.querySelectorAll('.btn-add-unidade').forEach(function(button) {
            button.addEventListener('click', function() {
                const unidadeId = this.getAttribute('data-unidade-id');
                const unidadeName = this.getAttribute('data-unidade-name');
                
                addUnidade(unidadeId, unidadeName);
            });
        });
        
        // Remover unidade via AJAX
        document.querySelectorAll('.btn-remove-unidade').forEach(function(button) {
            button.addEventListener('click', function() {
                const unidadeId = this.getAttribute('data-unidade-id');
                const unidadeName = this.getAttribute('data-unidade-name');
                
                removeUnidade(unidadeId, unidadeName);
            });
        });
        
        // Adicionar todas as unidades
        document.getElementById('addAllUnidades').addEventListener('click', function() {
            const unidades = document.querySelectorAll('#unidadesDisponiveisTable tr');
            if (unidades.length === 0) {
                return;
            }
            unidades.forEach(function(row) {
                const unidadeId = row.getAttribute('data-unidade-id');
                const unidadeName = row.querySelector('td').textContent.trim();
                
                addUnidade(unidadeId, unidadeName, false);
            });
        });
        
        // Remover todas as unidades
        document.getElementById('removeAllUnidades').addEventListener('click', function() {
            const unidades = document.querySelectorAll('#unidadesAssociadasTable tr');
            if (unidades.length === 0) {
                return;
            }
            
            unidades.forEach(function(row) {
                const unidadeId = row.getAttribute('data-unidade-id');
                const unidadeName = row.querySelector('td').textContent.trim();
                
                removeUnidade(unidadeId, unidadeName, false);
            });
        });
        
        // Funções para manipular unidades
        function addUnidade(unidadeId, unidadeName, showConfirmation = true) {
            document.getElementById('add_unidade_id').value = unidadeId;
            const form = document.getElementById('form-add-unidade');
            
            const formData = new FormData(form);
            
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
                    // Mover a unidade para a tabela de associadas
                    const row = document.querySelector(`#unidadesDisponiveisTable tr[data-unidade-id="${unidadeId}"]`);
                    if (row) {
                        // Criar a nova linha para a tabela de associadas
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-unidade-id', unidadeId);
                        newRow.innerHTML = `
                            <td>${unidadeName}</td>
                            <td class="text-right" style="width: 80px;">
                                <button type="button" class="btn btn-xs btn-danger btn-remove-unidade" data-unidade-id="${unidadeId}" data-unidade-name="${unidadeName}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        
                        // Adicionar evento ao novo botão
                        newRow.querySelector('.btn-remove-unidade').addEventListener('click', function() {
                            removeUnidade(unidadeId, unidadeName);
                        });
                        
                        // Adicionar à tabela de associadas
                        document.getElementById('unidadesAssociadasTable').appendChild(newRow);
                        
                        // Remover da tabela de disponíveis
                        row.remove();
                        
                        // Incrementar contador de modificações
                        document.getElementById('unidadesModificadas').value = 
                            parseInt(document.getElementById('unidadesModificadas').value) + 1;
                    }
                } else {
                    console.error('Erro ao adicionar unidade:', data.message || 'Erro desconhecido');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });
        }
        
        function removeUnidade(unidadeId, unidadeName, showConfirmation = true) {
            document.getElementById('remove_unidade_id').value = unidadeId;
            const form = document.getElementById('form-remove-unidade');
            
            const formData = new FormData(form);
            
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
                    // Mover a unidade para a tabela de disponíveis
                    const row = document.querySelector(`#unidadesAssociadasTable tr[data-unidade-id="${unidadeId}"]`);
                    if (row) {
                        // Criar a nova linha para a tabela de disponíveis
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-unidade-id', unidadeId);
                        newRow.innerHTML = `
                            <td>${unidadeName}</td>
                            <td class="text-right" style="width: 80px;">
                                <button type="button" class="btn btn-xs btn-success btn-add-unidade" data-unidade-id="${unidadeId}" data-unidade-name="${unidadeName}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </td>
                        `;
                        
                        // Adicionar evento ao novo botão
                        newRow.querySelector('.btn-add-unidade').addEventListener('click', function() {
                            addUnidade(unidadeId, unidadeName);
                        });
                        
                        // Adicionar à tabela de disponíveis
                        document.getElementById('unidadesDisponiveisTable').appendChild(newRow);
                        
                        // Remover da tabela de associadas
                        row.remove();
                        
                        // Incrementar contador de modificações
                        document.getElementById('unidadesModificadas').value = 
                            parseInt(document.getElementById('unidadesModificadas').value) + 1;
                    }
                } else {
                    console.error('Erro ao remover unidade:', data.message || 'Erro desconhecido');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });
        }
    });
</script>
@stop