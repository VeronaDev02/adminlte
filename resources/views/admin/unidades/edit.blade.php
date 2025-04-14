@extends('adminlte::page')

@section('content')
    @livewire('unidades.unidade-form', ['unidade' => $unidadeId])
@endsection