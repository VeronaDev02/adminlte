@extends('adminlte::page')

@section('content')
    @livewire('unidades.unidade-form-por-tipo', ['tipoCodigo' => $tipoCodigo, 'unidade' => $unidadeId])
@endsection