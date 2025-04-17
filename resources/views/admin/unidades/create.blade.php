@extends('adminlte::page')

@section('title', 'Criar Unidade')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('unidades.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active" style="font-weight: normal;">Criar Unidade</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @livewire('unidades.unidade-form')
@endsection