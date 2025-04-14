@extends('adminlte::page')
@include('components.alert.sweet-alert')

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
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $user->use_id) }}" method="POST" id="userForm">
                @csrf
                @method('PUT')
                
                <!-- Campos ocultos para armazenar as listas de unidades -->
                <input type="hidden" name="unidades_adicionar" id="unidades_adicionar" value="[]">
                <input type="hidden" name="unidades_remover" id="unidades_remover" value="[]">
                
                <div class="row mb-3">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_active">Ativo</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_active" name="use_active" value="1" {{ old('use_active', $user->use_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_active">SIM</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="use_cod_func">Código do Funcionário</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('use_cod_func') is-invalid @enderror" id="use_cod_func" name="use_cod_func" value="{{ old('use_cod_func', $user->use_cod_func) }}" placeholder="Código">
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
                            <input type="text" class="form-control @error('use_name') is-invalid @enderror" id="use_name" name="use_name" value="{{ old('use_name', $user->use_name) }}" required>
                            @error('use_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_username">Usuário</label>
                            <input type="text" class="form-control @error('use_username') is-invalid @enderror" id="use_username" name="use_username" value="{{ old('use_username', $user->use_username) }}" readonly>
                            @error('use_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_rol_id">Função</label>
                            <select class="form-control @error('use_rol_id') is-invalid @enderror" id="use_rol_id" name="use_rol_id" required>
                                <option value="">Selecione a Função</option>
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
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="use_email">Email (Opcional)</label>
                            <input type="email" class="form-control @error('use_email') is-invalid @enderror" id="use_email" name="use_email" value="{{ old('use_email', $user->use_email) }}">
                            @error('use_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="use_cell">Telefone (Opcional)</label>
                            <input type="text" class="form-control @error('use_cell') is-invalid @enderror" id="use_cell" name="use_cell" value="{{ old('use_cell', $user->use_cell) }}" placeholder="(00) 00000-0000">
                            @error('use_cell')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_password">Nova Senha (Opcional)</label>
                            <input type="password" class="form-control @error('use_password') is-invalid @enderror" id="use_password" name="use_password" placeholder="Deixe em branco para manter a senha atual">
                            @error('use_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Campos ocultos para manter os valores padrão -->
                    <input type="hidden" name="use_allow_updates" value="1">
                    <input type="hidden" name="use_login_ativo" id="use_login_ativo_hidden" value="{{ old('use_active', $user->use_active) }}">

                </div>
                
                <hr>
                
                <!-- Gerenciamento de Unidades -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Gerenciamento de Unidades</h5>
                    </div>
                    <div class="card-body">
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
                                                @foreach($unidadesDisponiveis as $unidade)
                                                <tr data-unidade-id="{{ $unidade->uni_id }}">
                                                    <td>{{ $unidade->uni_codigo }} - {{ $unidade->uni_descricao }} ({{ $unidade->uni_cidade }}/{{ $unidade->uni_uf }})</td>
                                                    <td class="text-right" style="width: 80px;">
                                                        <button type="button" class="btn btn-xs btn-success btn-add-unidade" 
                                                                data-unidade-id="{{ $unidade->uni_id }}" 
                                                                data-unidade-codigo="{{ $unidade->uni_codigo }}"
                                                                data-unidade-descricao="{{ $unidade->uni_descricao }}"
                                                                data-unidade-cidade="{{ $unidade->uni_cidade }}"
                                                                data-unidade-uf="{{ $unidade->uni_uf }}">
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
                                                @foreach($userUnidades as $unidade)
                                                <tr data-unidade-id="{{ $unidade->uni_id }}">
                                                    <td>{{ $unidade->uni_codigo }} - {{ $unidade->uni_descricao }} ({{ $unidade->uni_cidade }}/{{ $unidade->uni_uf }})</td>
                                                    <td class="text-right" style="width: 80px;">
                                                        <button type="button" class="btn btn-xs btn-danger btn-remove-unidade" 
                                                                data-unidade-id="{{ $unidade->uni_id }}" 
                                                                data-unidade-codigo="{{ $unidade->uni_codigo }}"
                                                                data-unidade-descricao="{{ $unidade->uni_descricao }}"
                                                                data-unidade-cidade="{{ $unidade->uni_cidade }}"
                                                                data-unidade-uf="{{ $unidade->uni_uf }}">
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
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="button" class="btn btn-primary" id="btnSalvar">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .custom-switch .custom-control-label::before {
            height: 1.25rem;
        }
        .custom-control-input:checked~.custom-control-label::before {
            color: #fff;
            border-color: #28a745;
            background-color: #28a745;
        }
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
    </style>
@stop
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Aplicar máscara do código do funcionário, por enquanto máximo 10 dígitos e só números
            $('#use_cod_func').inputmask({
                regex: '^[0-9]{1,10}$'
            });
            // Aplicar máscara ao telefone
            $('#use_cell').inputmask('(99) 99999-9999');
            
            $('#use_name').on('input', function() {
                let nome = $(this).val().trim();
                if (nome) {
                    // Dividir o nome em partes
                    let partes = nome.toLowerCase().split(' ');
                    
                    // Se tiver mais de uma parte no nome
                    if (partes.length > 1) {
                        // Pegar a primeira parte e a última
                        let primeiroNome = partes[0];
                        let ultimoNome = partes[partes.length - 1];
                        
                        // Criar o username como primeiro_ultimo conforme sugerido que é o padrão
                        let username = primeiroNome + '_' + ultimoNome;
                        
                        // Remover acentos e caracteres especiais
                        username = username.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                        
                        // Substituir espaços e caracteres especiais por _
                        username = username.replace(/[^a-z0-9]/g, '_');
                        
                        $('#use_username').val(username);
                    } else {
                        // Se for apenas uma palavra, usar ela mesma
                        let username = partes[0].normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                        username = username.replace(/[^a-z0-9]/g, '_');
                        $('#use_username').val(username);
                    }
                } else {
                    $('#use_username').val('');
                }
            });
            // Arrays para controlar as unidades adicionadas e removidas
            let unidadesParaAdicionar = [];
            let unidadesParaRemover = [];
            
            // Toggle do switch de ativo
            $('#use_active').change(function() {
                const isChecked = $(this).is(':checked');
                $('#use_login_ativo_hidden').val(isChecked ? 1 : 0);
                $(this).next('label').text(isChecked ? 'SIM' : 'NÃO');
            });
            
            // Inicializar os valores corretos para o label do switch
            if ($('#use_active').is(':checked')) {
                $('#use_active').next('label').text('SIM');
            } else {
                $('#use_active').next('label').text('NÃO');
            }
            
            // Buscar funcionário pelo código
            $('#buscarFuncionario').click(function() {
                const codigo = $('#use_cod_func').val();
                if (codigo) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Buscando funcionário',
                        text: 'Aguarde enquanto buscamos as informações...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Fazer requisição AJAX para o controller
                    $.ajax({
                        url: "/users/get-funcionario",
                        method: 'GET',
                        data: {
                            use_cod_func: codigo
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            
                            if (response && response.name) {
                                $('#use_name').val(response.name);
                                $('#use_name').trigger('input');
                                
                                Swal.fire({
                                    title: 'Sucesso',
                                    text: 'Informações do funcionário encontradas!',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Aviso',
                                    text: 'Não foram encontradas informações para o código informado.',
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            
                            let mensagem = 'Erro ao buscar funcionário.';
                            
                            // Para depuração
                            console.error('Status:', status);
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);
                            
                            try {
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    mensagem = xhr.responseJSON.error;
                                } else if (xhr.responseText) {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.error) {
                                        mensagem = response.error;
                                    }
                                }
                            } catch (e) {
                                console.error('Erro ao processar resposta:', e);
                            }
                            
                            Swal.fire({
                                title: 'Erro',
                                text: mensagem,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Erro',
                        text: 'Por favor, insira um código de funcionário para buscar',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            });
            
            // Pesquisar nas tabelas de unidades
            $('#searchUnidadesDisponiveis').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                $('#unidadesDisponiveisTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });
            
            $('#searchUnidadesAssociadas').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                $('#unidadesAssociadasTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });
            
            // Adicionar unidade individual
            $(document).on('click', '.btn-add-unidade', function() {
                const unidadeId = $(this).data('unidade-id');
                const unidadeCodigo = $(this).data('unidade-codigo');
                const unidadeDescricao = $(this).data('unidade-descricao');
                const unidadeCidade = $(this).data('unidade-cidade');
                const unidadeUf = $(this).data('unidade-uf');
                
                // Remover a linha da tabela de disponíveis
                $(this).closest('tr').remove();
                
                // Adicionar à tabela de associadas
                const newRow = `
                    <tr data-unidade-id="${unidadeId}">
                        <td>${unidadeCodigo} - ${unidadeDescricao} (${unidadeCidade}/${unidadeUf})</td>
                        <td class="text-right" style="width: 80px;">
                            <button type="button" class="btn btn-xs btn-danger btn-remove-unidade" 
                                    data-unidade-id="${unidadeId}"
                                    data-unidade-codigo="${unidadeCodigo}"
                                    data-unidade-descricao="${unidadeDescricao}"
                                    data-unidade-cidade="${unidadeCidade}"
                                    data-unidade-uf="${unidadeUf}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#unidadesAssociadasTable').append(newRow);
                
                // Atualizar listas de controle
                if (!unidadesParaAdicionar.includes(unidadeId)) {
                    unidadesParaAdicionar.push(unidadeId);
                }
                
                // Se estava na lista de remoção, remover de lá
                const indexToRemove = unidadesParaRemover.indexOf(unidadeId);
                if (indexToRemove > -1) {
                    unidadesParaRemover.splice(indexToRemove, 1);
                }
                
                updateHiddenFields();
            });
            
            // Remover unidade individual
            $(document).on('click', '.btn-remove-unidade', function() {
                const unidadeId = $(this).data('unidade-id');
                const unidadeCodigo = $(this).data('unidade-codigo');
                const unidadeDescricao = $(this).data('unidade-descricao');
                const unidadeCidade = $(this).data('unidade-cidade');
                const unidadeUf = $(this).data('unidade-uf');
                
                // Remover a linha da tabela de associadas
                $(this).closest('tr').remove();
                
                // Adicionar à tabela de disponíveis
                const newRow = `
                    <tr data-unidade-id="${unidadeId}">
                        <td>${unidadeCodigo} - ${unidadeDescricao} (${unidadeCidade}/${unidadeUf})</td>
                        <td class="text-right" style="width: 80px;">
                            <button type="button" class="btn btn-xs btn-success btn-add-unidade" 
                                    data-unidade-id="${unidadeId}"
                                    data-unidade-codigo="${unidadeCodigo}"
                                    data-unidade-descricao="${unidadeDescricao}"
                                    data-unidade-cidade="${unidadeCidade}"
                                    data-unidade-uf="${unidadeUf}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#unidadesDisponiveisTable').append(newRow);
                
                // Atualizar listas de controle
                if (!unidadesParaRemover.includes(unidadeId)) {
                    unidadesParaRemover.push(unidadeId);
                }
                
                // Se estava na lista de adição, remover de lá
                const indexToRemove = unidadesParaAdicionar.indexOf(unidadeId);
                if (indexToRemove > -1) {
                    unidadesParaAdicionar.splice(indexToRemove, 1);
                }
                
                updateHiddenFields();
            });
            
            // Adicionar todas as unidades
            $('#addAllUnidades').click(function() {
                $('#unidadesDisponiveisTable tr').each(function() {
                    const btn = $(this).find('.btn-add-unidade');
                    if (btn.length) {
                        btn.trigger('click');
                    }
                });
            });
            
            // Remover todas as unidades
            $('#removeAllUnidades').click(function() {
                $('#unidadesAssociadasTable tr').each(function() {
                    const btn = $(this).find('.btn-remove-unidade');
                    if (btn.length) {
                        btn.trigger('click');
                    }
                });
            });
            
            // Atualizar campos ocultos com as listas JSON de unidades
            function updateHiddenFields() {
                $('#unidades_adicionar').val(JSON.stringify(unidadesParaAdicionar));
                $('#unidades_remover').val(JSON.stringify(unidadesParaRemover));
            }
            
            // Validação e submissão do formulário
            $('#btnSalvar').click(function() {
                // Verificações básicas
                if (!$('#use_name').val()) {
                    Swal.fire('Atenção', 'O nome do usuário é obrigatório.', 'warning');
                    return;
                }
                
                if (!$('#use_cod_func').val()) {
                    Swal.fire('Atenção', 'O código do funcionário é obrigatório.', 'warning');
                    return;
                }
                
                if (!$('#use_username').val()) {
                    Swal.fire('Atenção', 'O nome de usuário é obrigatório.', 'warning');
                    return;
                }
                
                if (!$('#use_rol_id').val()) {
                    Swal.fire('Atenção', 'A função do usuário é obrigatória.', 'warning');
                    return;
                }
                
                // Verificar senha
                const password = $('#use_password').val();
                if (password && password.length < 6) {
                    Swal.fire('Atenção', 'A senha deve ter no mínimo 6 caracteres', 'warning');
                    return;
                }
                
                // Verificar email (se fornecido)
                const email = $('#use_email').val();
                if (email) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        Swal.fire('Atenção', 'O email informado é inválido', 'warning');
                        return;
                    }
                }
                
                // Confirmar antes de salvar
                Swal.fire({
                    title: 'Confirmar alterações?',
                    text: "As alterações serão salvas no banco de dados",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, salvar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Verificar se há valores nos campos ocultos
                        if (unidadesParaAdicionar.length === 0 && unidadesParaRemover.length === 0) {
                            // Se não houver alterações nas unidades, definir os arrays como vazios
                            $('#unidades_adicionar').val('[]');
                            $('#unidades_remover').val('[]');
                        }
                        
                        // Submeter o formulário
                        $('#userForm').submit();
                    }
                });
            });
            
            // Para edição: verificar se o username e email são únicos (exceto para o usuário atual)
            $('#use_username, #use_email').on('blur', function() {
                const field = $(this).attr('id');
                const value = $(this).val();
                
                if (!value) return; // Não validar se estiver vazio
                
                $.ajax({
                    url: "{{ route('users.update', $user->use_id) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PUT',
                        [field]: value,
                        validate_only: true
                    },
                    success: function(response) {
                        if (!response.valid && response.errors[field]) {
                            Swal.fire('Erro', response.errors[field], 'error');
                            $(`#${field}`).addClass('is-invalid').focus();
                        } else {
                            $(`#${field}`).removeClass('is-invalid');
                        }
                    },
                    error: function(xhr) {
                        console.error('Erro na validação:', xhr);
                    }
                });
            });
        });
    </script>
@stop