@extends('adminlte::page')

@section('plugins.Toastr', true)
@section('plugins.Select2', true)
@section('title', 'Editar SelfCheckout')

@section('content')
    @livewire('selfs.self-checkout-form', ['self' => $self])
@stop