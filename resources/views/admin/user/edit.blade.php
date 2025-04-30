@extends('adminlte::page')

@section('title', 'Editar Usuário')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">Usuários</a></li>
                <li class="breadcrumb-item active">Editar Usuário</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Editar Usuário</h3>
            </div>
        </div>

        <div class="card-body">
            <x-Admin.User.Forms.FormUser :user="$user" :unidades="$unidades" :editMode=true :roles="$roles" />
        </div>
    </div>
@stop
