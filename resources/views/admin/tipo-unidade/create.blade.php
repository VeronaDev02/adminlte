@extends('adminlte::page')

@section('content')
    @livewire('unidades.unidade-form-por-tipo', ['tipoCodigo' => $codigo])
@endsection