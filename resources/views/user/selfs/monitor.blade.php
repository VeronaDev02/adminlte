@extends('adminlte::page')

@section('title', $pageTitle ?? 'Monitoramento de PDVs')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid p-0">
        <button id="browserFullscreenBtn" onclick="toggleBrowserFullscreen()">
            <i class="fas fa-desktop"></i> Tela Cheia (F11)
        </button>
        
        <livewire:selfs.self-monitor-screen />
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/monitor.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/monitor.js') }}"></script>
@stop