@extends('adminlte::page')

@section('title', 'Nova Função')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('roles.index') }}">Funções</a></li>
                <li class="breadcrumb-item active" style="font-weight: normal;">Nova Função</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Nova Função</h3>
            </div>
        </div>

        <div class="card-body">
            @livewire('roles.role-form')
        </div>
    </div>
@stop