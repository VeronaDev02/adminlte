@extends('adminlte::page')
@include('components.alert.sweet-alert')

@section('title', 'Criar Usuário')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Novo Usuário</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
                <li class="breadcrumb-item active" aria-current="page">Criar</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST" id="userForm">
                @csrf
                
                <!-- Campos ocultos para armazenar as listas de unidades -->
                <input type="hidden" name="unidades_adicionar" id="unidades_adicionar" value="[]">
                <input type="hidden" name="unidades_remover" id="unidades_remover" value="[]">
                
                <div class="row mb-3">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_active">Ativo</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_active" name="use_active" value="1" {{ old('use_active', '1') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_active">SIM</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="use_cod_func">Código do Funcionário</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('use_cod_func') is-invalid @enderror" id="use_cod_func" name="use_cod_func" value="{{ old('use_cod_func') }}" placeholder="Código">
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
                            <input type="text" class="form-control @error('use_name') is-invalid @enderror" id="use_name" name="use_name" value="{{ old('use_name') }}" required>
                            @error('use_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_username">Usuário</label>
                            <input type="text" class="form-control @error('use_username') is-invalid @enderror" id="use_username" name="use_username" value="{{ old('use_username') }}" readonly>
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
                                    <option value="{{ $role->rol_id }}" {{ old('use_rol_id') == $role->rol_id ? 'selected' : '' }}>
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
                            <input type="email" class="form-control @error('use_email') is-invalid @enderror" id="use_email" name="use_email" value="{{ old('use_email') }}">
                            @error('use_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="use_cell">Telefone (Opcional)</label>
                            <input type="text" class="form-control @error('use_cell') is-invalid @enderror" id="use_cell" name="use_cell" value="{{ old('use_cell') }}" placeholder="(00) 00000-0000">
                            @error('use_cell')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="use_password">Senha</label>
                            <input type="password" class="form-control @error('use_password') is-invalid @enderror" id="use_password" name="use_password" required>
                            @error('use_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Campos ocultos para manter os valores padrão -->
                    <input type="hidden" name="use_allow_updates" value="1">
                    <input type="hidden" name="use_login_ativo" id="use_login_ativo_hidden" value="{{ old('use_active', '1') }}">

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
                                                @foreach($unidades as $unidade)
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
                                                <!-- Unidades associadas serão adicionadas aqui daí -->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Definir URLs 
    var processarUnidadesUrl = "{{ route('users.processar-unidades', ':id') }}";
    var usersIndexUrl = "{{ route('users.index') }}";
    
    // Variáveis globais para tracking de unidades
    var unidadesParaAdicionar = [];
    var unidadesParaRemover = [];

    $(document).ready(function() {

        // Máscara pro código do funcinonário
        $('#use_cod_func').inputmask({
            regex: '^[0-9]{1,10}$'
        });

        // Máscara pro telefone
        $('#use_cell').inputmask('(99) 99999-9999');
        
        // Função para gerar o username a partir do nome
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
                    
                    // Criar o username como primeiro_ultimo, do jeito que é o padrão
                    let username = primeiroNome + '_' + ultimoNome;
                    
                    // Remover acentos e caracteres especiais
                    username = username.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                    
                    // Substituir espaços e caracteres por _
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
        
        // Botão de buscar funcionário
        $('#buscarFuncionario').on('click', function() {
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
        
        // Pesquisa para Unidades Disponíveis
        $('#searchUnidadesDisponiveis').on('keyup', function() {
            filterTable('searchUnidadesDisponiveis', 'unidadesDisponiveisTable');
        });
        
        // Pesquisa para Unidades Associadas
        $('#searchUnidadesAssociadas').on('keyup', function() {
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
        
        // Adicionar unidade localmente
        $(document).on('click', '.btn-add-unidade', function() {
            const unidadeId = $(this).data('unidade-id');
            const unidadeCodigo = $(this).data('unidade-codigo');
            const unidadeDescricao = $(this).data('unidade-descricao');
            const unidadeCidade = $(this).data('unidade-cidade');
            const unidadeUf = $(this).data('unidade-uf');
            
            addUnidadeLocally(unidadeId, unidadeCodigo, unidadeDescricao, unidadeCidade, unidadeUf);
        });
        
        // Remover unidade localmente
        $(document).on('click', '.btn-remove-unidade', function() {
            const unidadeId = $(this).data('unidade-id');
            const unidadeCodigo = $(this).data('unidade-codigo');
            const unidadeDescricao = $(this).data('unidade-descricao');
            const unidadeCidade = $(this).data('unidade-cidade');
            const unidadeUf = $(this).data('unidade-uf');
            
            removeUnidadeLocally(unidadeId, unidadeCodigo, unidadeDescricao, unidadeCidade, unidadeUf);
        });
        
        // Adicionar todas as unidades
        $('#addAllUnidades').on('click', function() {
            const unidades = $('#unidadesDisponiveisTable tr');
            if (unidades.length === 0) {
                Swal.fire({
                    title: 'Aviso',
                    text: 'Não há unidades disponíveis para adicionar.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            unidades.each(function() {
                const btn = $(this).find('.btn-add-unidade');
                const unidadeId = btn.data('unidade-id');
                const unidadeCodigo = btn.data('unidade-codigo');
                const unidadeDescricao = btn.data('unidade-descricao');
                const unidadeCidade = btn.data('unidade-cidade');
                const unidadeUf = btn.data('unidade-uf');
                
                addUnidadeLocally(unidadeId, unidadeCodigo, unidadeDescricao, unidadeCidade, unidadeUf);
            });
        });
        
        // Remover todas as unidades
        $('#removeAllUnidades').on('click', function() {
            const unidades = $('#unidadesAssociadasTable tr');
            if (unidades.length === 0) {
                Swal.fire({
                    title: 'Aviso',
                    text: 'Não há unidades associadas para remover.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            unidades.each(function() {
                const btn = $(this).find('.btn-remove-unidade');
                const unidadeId = btn.data('unidade-id');
                const unidadeCodigo = btn.data('unidade-codigo');
                const unidadeDescricao = btn.data('unidade-descricao');
                const unidadeCidade = btn.data('unidade-cidade');
                const unidadeUf = btn.data('unidade-uf');
                
                removeUnidadeLocally(unidadeId, unidadeCodigo, unidadeDescricao, unidadeCidade, unidadeUf);
            });
        });
        
        // Evento para o botão salvar
        $('#btnSalvar').on('click', function() {
            // Preencher os campos ocultos com os arrays de unidades
            $('#use_login_ativo_hidden').val($('#use_active').is(':checked') ? 1 : 0);
            $('#unidades_adicionar').val(JSON.stringify(unidadesParaAdicionar.length ? unidadesParaAdicionar : []));
            $('#unidades_remover').val(JSON.stringify(unidadesParaRemover.length ? unidadesParaRemover : []));
            
            // Log para debugar (remover depois)
            console.log('Unidades para adicionar:', unidadesParaAdicionar);
            console.log('Unidades para remover:', unidadesParaRemover);
            
            // Validar campos obrigatórios
            const camposObrigatorios = [
                '#use_name', 
                '#use_username', 
                '#use_rol_id', 
                '#use_password'
            ];
            
            let todosCamposPreenchidos = true;
            
            camposObrigatorios.forEach(function(campo) {
                if (!$(campo).val()) {
                    $(campo).addClass('is-invalid');
                    todosCamposPreenchidos = false;
                } else {
                    $(campo).removeClass('is-invalid');
                }
            });
            
            // Se todos os campos obrigatórios estiverem preenchidos
            if (todosCamposPreenchidos) {
                // Submeter o formulário com os dados do usuário e arrays de unidades
                const form = document.getElementById('userForm');
                const btnSalvar = document.getElementById('btnSalvar');
                btnSalvar.disabled = true;
                btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
                
                form.submit();
            } else {
                Swal.fire({
                    title: 'Erro',
                    text: 'Por favor, preencha todos os campos obrigatórios.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
        
        // Funções para adicionar e remover unidades localmente
        function addUnidadeLocally(unidadeId, unidadeCodigo, unidadeDescricao, unidadeCidade, unidadeUf) {
            // Verificar se a unidade já foi adicionada
            if (unidadesParaAdicionar.includes(unidadeId)) {
                return;
            }
            
            // Remover da lista de remoção se estiver lá
            const indexRemover = unidadesParaRemover.indexOf(unidadeId);
            if (indexRemover !== -1) {
                unidadesParaRemover.splice(indexRemover, 1);
            }
            
            // Adicionar à lista de adição
            unidadesParaAdicionar.push(unidadeId);
            
            // Mover a unidade para a tabela de associadas (apenas visualmente)
            const row = $(`#unidadesDisponiveisTable tr[data-unidade-id="${unidadeId}"]`);
            if (row.length) {
                // Criar a nova linha para a tabela de associadas
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
                
                // Adicionar à tabela de associadas
                $('#unidadesAssociadasTable').append(newRow);
                
                // Remover da tabela de disponíveis
                row.remove();
            }
        }
        
        function removeUnidadeLocally(unidadeId, unidadeCodigo, unidadeDescricao, unidadeCidade, unidadeUf) {
            // Verificar se a unidade já foi removida
            if (unidadesParaRemover.includes(unidadeId)) {
                return;
            }
            
            // Remover da lista de adição se estiver lá
            const indexAdicionar = unidadesParaAdicionar.indexOf(unidadeId);
            if (indexAdicionar !== -1) {
                unidadesParaAdicionar.splice(indexAdicionar, 1);
            }
            
            // Adicionar à lista de remoção
            unidadesParaRemover.push(unidadeId);
            
            // Mover a unidade para a tabela de disponíveis (apenas visualmente)
            const row = $(`#unidadesAssociadasTable tr[data-unidade-id="${unidadeId}"]`);
            if (row.length) {
                // Criar a nova linha para a tabela de disponíveis
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
                            </button></td></tr>`;
                
                // Adicionar à tabela de disponíveis
                $('#unidadesDisponiveisTable').append(newRow);
                
                // Remover da tabela de associadas
                row.remove();
            }
        }
    });
</script>
@stop