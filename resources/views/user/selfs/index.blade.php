@extends('adminlte::page')

@section('title', 'SelfCheckouts')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>SelfCheckouts</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <livewire:selfs.self-config/>
@stop