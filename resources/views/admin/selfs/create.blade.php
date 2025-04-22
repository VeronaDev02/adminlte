@extends('adminlte::page')

@section('plugins.Toastr', true)
@section('plugins.Select2', true)
@section('title', 'Criar Novo SelfCheckout')

@section('content')
    @livewire('selfs.self-checkout-form')
@stop