@extends('adminlte::page')

@section('content')
    @livewire('unidades.unidade-form-por-tipo', ['tipoCodigo' => $codigo, 'unidade' => $unidadeId])
@endsection