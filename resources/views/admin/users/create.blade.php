@extends('adminlte::page')

@section('title', 'Criar Usuário')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Criar Novo Usuário</h1>
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
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_name">Nome</label>
                            <input type="text" class="form-control @error('use_name') is-invalid @enderror" id="use_name" name="use_name" value="{{ old('use_name') }}" required>
                            @error('use_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_username">Username</label>
                            <input type="text" class="form-control @error('use_username') is-invalid @enderror" id="use_username" name="use_username" value="{{ old('use_username') }}" required>
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
                            <input type="email" class="form-control @error('use_email') is-invalid @enderror" id="use_email" name="use_email" value="{{ old('use_email') }}" required>
                            @error('use_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_password">Senha</label>
                            <input type="password" class="form-control @error('use_password') is-invalid @enderror" id="use_password" name="use_password" required>
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
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="use_cod_func">Código do Funcionário</label>
                            <input type="text" class="form-control @error('use_cod_func') is-invalid @enderror" id="use_cod_func" name="use_cod_func" value="{{ old('use_cod_func') }}">
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
                                <input type="checkbox" class="custom-control-input" id="use_active" name="use_active" value="1" {{ old('use_active', '1') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_active">Usuário Ativo</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_login_ativo" name="use_login_ativo" value="1" {{ old('use_login_ativo', '1') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_login_ativo">Login Ativo</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_allow_updates" name="use_allow_updates" value="1" {{ old('use_allow_updates', '1') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_allow_updates">Permitir Updates</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Gerenciamento de Unidades -->
                <h4 class="mt-4 mb-3">Gerenciamento de Unidades</h4>
                <p class="text-muted mb-4">Você poderá associar unidades ao usuário após salvá-lo.</p>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
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
    </style>
@stop