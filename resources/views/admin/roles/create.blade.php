@extends('adminlte::page')

@section('title', 'Criar Cargo/Função')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">Criar Novo Cargo Função</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Cargos/Funções</a></li>
                <li class="breadcrumb-item active" aria-current="page">Criar</li>
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
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="rol_name">Nome do Cargo/Função <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('rol_name') is-invalid @enderror" id="rol_name" name="rol_name" value="{{ old('rol_name') }}" required>
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
    </style>
@stop