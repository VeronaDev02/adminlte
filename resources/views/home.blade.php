@extends('adminlte::page')

@section('title', 'Dashboard do Usuário')

@section('content_header')
    <h1>Dashboard do Usuário</h1>
@stop

@section('content')
    <!-- Mensagem de Boas-vindas e Status -->
    <div class="row">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Card de Boas-vindas e Informações do Usuário -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Bem-vindo(a)</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if(Auth::user()->img_user)
                            <img src="{{ Auth::user()->img_user }}" class="img-circle mr-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Imagem do usuário">
                        @else
                            <div class="img-circle mr-3 d-flex align-items-center justify-content-center bg-primary" style="width: 70px; height: 70px;">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="mb-1">{{ Auth::user()->use_name ?? Auth::user()->name }}</h4>
                            <p class="text-muted mb-0">
                                <i class="fas fa-envelope mr-1"></i> {{ Auth::user()->email }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('user.profile') }}" class="btn btn-sm btn-primary mr-1">
                            <i class="fas fa-user-edit mr-1"></i> Editar Perfil
                        </a>
                        <a href="{{ route('user.redefinirSenhaPage') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-lock mr-1"></i> Alterar Senha
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Acesso Rápido -->
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Acesso Rápido</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-md-4 text-center mb-3">
                            <a href="{{ route('selfcheckout.index') }}" class="btn btn-app bg-warning">
                                <i class="fas fa-desktop"></i> Self Checkouts
                            </a>
                        </div>
                        @if(isset(Auth::user()->ui_preferences['tela']) && count(Auth::user()->ui_preferences['tela']) > 0)
                            <div class="col-6 col-md-4 text-center mb-3">
                                <a href="{{ url(route('selfs.monitor', [
                                    'quadrants' => Auth::user()->ui_preferences['tela'][0]['quadrants'],
                                    'cols' => Auth::user()->ui_preferences['tela'][0]['columns'],
                                    'rows' => Auth::user()->ui_preferences['tela'][0]['rows']
                                ])) }}" class="btn btn-app bg-success">
                                    <i class="fas fa-tv"></i> Monitor 1
                                </a>
                            </div>
                        @endif
                        <div class="col-6 col-md-4 text-center mb-3">
                            <a href="{{ route('user.profile') }}" class="btn btn-app bg-primary">
                                <i class="fas fa-user-cog"></i> Perfil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Telas Salvas -->
    @if(isset(Auth::user()->ui_preferences['tela']) && count(Auth::user()->ui_preferences['tela']) > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Suas Telas Salvas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach(Auth::user()->ui_preferences['tela'] as $index => $tela)
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-tv"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Monitor {{ $index + 1 }}</span>
                                            <span class="info-box-number">
                                                {{ $tela['quadrants'] }} quadrantes ({{ $tela['columns'] }}x{{ $tela['rows'] }})
                                            </span>
                                            <div class="mt-2">
                                                <a href="{{ url(route('selfs.monitor', [
                                                    'quadrants' => $tela['quadrants'],
                                                    'cols' => $tela['columns'],
                                                    'rows' => $tela['rows']
                                                ])) }}" class="btn btn-xs btn-success mr-1">
                                                    <i class="fas fa-eye"></i> Visualizar
                                                </a>
                                                <button type="button" class="btn btn-xs btn-danger" 
                                                    onclick="event.preventDefault(); 
                                                    if(confirm('Tem certeza que deseja excluir esta tela?')) { 
                                                        deleteTela({{ $index }}) 
                                                    }">
                                                    <i class="fas fa-trash"></i> Excluir
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Dicas e Status do Sistema -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">Dicas e Informações</h3>
                </div>
                <div class="card-body">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info-circle"></i> Como usar o sistema:</h5>
                        <p>Para criar um novo monitor de Self Checkouts, acesse o menu <strong>Self Checkouts</strong> e configure as suas preferências. Você pode definir o número de quadrantes, colunas e linhas, além de selecionar quais PDVs deseja monitorar.</p>
                    </div>
                    <div class="callout callout-warning">
                        <h5><i class="fas fa-lightbulb"></i> Dica:</h5>
                        <p>Você pode salvar várias configurações de monitor e acessá-las rapidamente pelo menu lateral ou por esta dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            margin-bottom: 1rem;
        }
        .card-primary .card-header {
            background-color: #007bff;
            color: white;
        }
        .card-success .card-header {
            background-color: #28a745;
            color: white;
        }
        .card-info .card-header {
            background-color: #17a2b8;
            color: white;
        }
        .card-light .card-header {
            background-color: #f8f9fa;
            color: #1f2d3d;
        }
        .btn-app {
            height: 80px;
            width: 80px;
            font-size: 12px;
            padding-top: 20px;
        }
        .btn-app i {
            font-size: 25px;
            display: block;
            margin-bottom: 5px;
        }
        .info-box-content .btn-xs {
            padding: .125rem .25rem;
            font-size: .75rem;
            line-height: 1.5;
        }
    </style>
@stop

@section('js')
    <script>
        // Função para deletar uma tela salva
        function deleteTela(index) {
            fetch(`/user/destroy-tela-preferences/${index}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recarregar a página após excluir com sucesso
                    window.location.reload();
                } else {
                    alert('Erro ao excluir: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao excluir a tela.');
            });
        }

        // Inicializar tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop