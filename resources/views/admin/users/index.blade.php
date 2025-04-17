@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Toastr', true)
@section('title', 'Usuários')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" style="font-weight: normal;">Usuários</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('users.user-list')
@stop