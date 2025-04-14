@extends('adminlte::page')

@section('content')
    @livewire('users.user-form', ['user' => $userId])
@endsection