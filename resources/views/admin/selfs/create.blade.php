@extends('adminlte::page')

@section('title', 'Novo Usuário')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.selfs.index') }}">SelfCheckouts</a></li>
                <li class="breadcrumb-item active">Novo SelfCheckout</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Novo SelfCheckout</h3>
            </div>
        </div>

        <div class="card-body">
            <x-Admin.Selfs.Forms.FormSelfs :unidades="$unidades" :editMode=false />
        </div>
    </div>
@stop