@extends('adminlte::page')

@section('title', 'Editar Usuário')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.unidade.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active">Editar Usuário</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Editar Unidade</h3>
            </div>
        </div>

        <div class="card-body">
            <x-Admin.Unidade.Forms.FormUnidade :unidade="$unidade" :tiposUnidade="$tiposUnidade" :usuarios="$usuarios" :editMode=true />
        </div>
    </div>
@stop