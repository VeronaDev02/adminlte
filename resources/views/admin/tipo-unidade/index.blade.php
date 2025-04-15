@extends('adminlte::page')

@section('content')
    @livewire('unidades.unidade-por-tipo-list', ['tipoCodigo' => $codigo])
@endsection