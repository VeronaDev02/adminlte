@extends('adminlte::page')

@section('content')
    @livewire('selfs.self-checkout-form', ['self' => $self])
@endsection