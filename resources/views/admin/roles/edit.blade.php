@extends('adminlte::page')

@section('title', 'Editar Função')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('roles.index') }}">Funções</a></li>
                <li class="breadcrumb-item active" style="font-weight: normal;">Editar Função</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Editar Função</h3>
            </div>
        </div>

        <div class="card-body">
            @livewire('roles.role-form', ['role' => $roleId])
        </div>
    </div>
@stop