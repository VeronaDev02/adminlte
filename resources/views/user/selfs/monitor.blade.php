@extends('adminlte::page')

@section('title', 'Monitoramento de PDVs')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Monitoramento de PDVs</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid p-0">
        <button id="browserFullscreenBtn" onclick="toggleBrowserFullscreen()">
            <i class="fas fa-desktop "></i> Tela Cheia (F11)
        </button>
        
        <livewire:selfs.self-monitor-screen />
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/monitor.css') }}">
@stop

@section('js')
    <script type="module">
        console.log('Caminho base JS:', '{{ asset('js/monitor/') }}');

        // Exponha a função de fullscreen globalmente
        import('/js/monitor/fullscreen.js').then(module => {
            console.log('Módulo de fullscreen importado');
            window.toggleBrowserFullscreen = module.toggleBrowserFullscreen;
            console.log('toggleBrowserFullscreen exposto globalmente');
        }).catch(error => {
            console.error('Erro ao importar fullscreen:', error);
        });

        // Verificação de módulos
        window.addEventListener('DOMContentLoaded', () => {
            console.log('Verificando módulos...');
            Promise.all([
                import('/js/monitor/config.js'),
                // .then(module => console.log('Config carregado', module)),
                import('/js/monitor/connection.js'),
                // .then(module => console.log('Connection carregado', module)),
                import('/js/monitor/ui.js'),
                // .then(module => console.log('UI carregado', module)),
                import('/js/monitor/fullscreen.js')
                // .then(module => console.log('Fullscreen carregado', module))
            ]).catch(error => console.error('Erro ao carregar módulos:', error));
        });
    </script>
    <script type="module" src="{{ asset('js/monitor/index.js') }}"></script>
@stop