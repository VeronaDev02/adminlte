@extends('adminlte::page')

@section('title', 'Editar Cargo/Função')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Editar Cargo/Função</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Cargos e Funções</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informações do Cargo/Função</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('roles.update', $role->rol_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="rol_name">Nome do Cargo/Função <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('rol_name') is-invalid @enderror" id="rol_name" name="rol_name" value="{{ old('rol_name', $role->rol_name) }}" required>
                    @error('rol_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="mt-4 text-right">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Usuários associados com o Cargo/Função</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Username</th>
                            <th>Unidade</th>
                            <th>Data Criação</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($role->users as $user)
                        <tr>
                            <td>{{ $user->use_id }}</td>
                            <td>{{ $user->use_name }}</td>
                            <td>{{ $user->use_username }}</td>
                            <td>
                                @if($user->unidade)
                                {{ $user->unidade->uni_codigo }} - {{ $user->unidade->uni_descricao }}
                                @else
                                <span class="text-muted">Não definida</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @if($user->use_active)
                                <span class="badge bg-success">Ativo</span>
                                @else
                                <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhum usuário encontrado para este cargo/função</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-title {
            font-weight: bold;
        }
        .form-group label {
            font-weight: bold;
        }
        .text-danger {
            color: #dc3545!important;
        }
        .badge-success, .bg-success {
            background-color: #28a745;
        }
        .badge-danger, .bg-danger {
            background-color: #dc3545;
        }
        .badge {
            display: inline-block;
            padding: .25em .4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            color: white;
        }
    </style>
@stop