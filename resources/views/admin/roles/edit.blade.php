@extends('adminlte::page')

@section('content')
    @livewire('roles.role-form', ['role' => $roleId])
@endsection