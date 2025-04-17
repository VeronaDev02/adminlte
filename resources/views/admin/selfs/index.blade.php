@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Toastr', true)
@section('title', 'SelfCheckouts')

@section('content')
    @livewire('selfs.self-checkout-list')
@stop