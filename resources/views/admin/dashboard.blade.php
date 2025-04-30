@extends('adminlte::page')

@section('title', 'Dashboard Administrativo')

@section('content_header')
    <h1>Dashboard Administrativo</h1>
@stop

@section('content')
    <!-- Cards de Estatísticas -->
    <div class="row">
        <!-- Total de Usuários -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Usuários</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.user.index') }}" class="small-box-footer">
                    Mais informações <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <!-- Total de Unidades -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalUnits }}</h3>
                    <p>Unidades</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="{{ route('admin.unidade.index') }}" class="small-box-footer">
                    Mais informações <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <!-- Total de Self Checkouts -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalSelfs }}</h3>
                    <p>Self Checkouts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <a href="{{ route('admin.selfs.index') }}" class="small-box-footer">
                    Mais informações <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <!-- Total de Funções (Roles) -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalRoles }}</h3>
                    <p>Funções</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tag"></i>
                </div>
                <a href="{{ route('admin.role.index') }}" class="small-box-footer">
                    Mais informações <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de Usuários por Role -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Distribuição de Usuários por Função</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="usersByRoleChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Gráfico de Status dos Self Checkouts -->
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Status dos Self Checkouts</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="selfsByStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mt-3">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <span class="mr-2" style="background-color:#28a745; width:15px; height:15px; display:inline-block;"></span>
                                    <span>Ativos: {{ $activeSelfs }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="mr-2" style="background-color:#dc3545; width:15px; height:15px; display:inline-block;"></span>
                                    <span>Inativos: {{ $inactiveSelfs }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Self Checkouts por Unidade -->
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Self Checkouts por Unidade</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="selfsByUnitChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Gráfico de Usuários por Unidade -->
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Usuários por Unidade</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="usersByUnitChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo do Sistema -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resumo do Sistema</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Usuários Ativos</span>
                                    <span class="info-box-number">{{ $activeUsers }} de {{ $totalUsers }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" style="width: {{ $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-cash-register"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Self Checkouts Ativos</span>
                                    <span class="info-box-number">{{ $activeSelfs }} de {{ $totalSelfs }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: {{ $totalSelfs > 0 ? ($activeSelfs / $totalSelfs) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Bem-vindo à Administração do Sistema</h5>
                                    <p>Você está logado como <strong>{{ Auth::user()->use_name }}</strong>.</p>
                                    <p>Suas unidades: <strong>{{ $userUnidades ?: 'Nenhuma unidade associada' }}</strong></p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-primary mr-1">
                                            <i class="fas fa-users mr-1"></i> Usuários
                                        </a>
                                        <a href="{{ route('admin.unidade.index') }}" class="btn btn-sm btn-success mr-1">
                                            <i class="fas fa-building mr-1"></i> Unidades
                                        </a>
                                        <a href="{{ route('admin.selfs.index') }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-cash-register mr-1"></i> Self Checkouts
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-primary .card-header {
            background-color: #007bff;
        }
        .card-success .card-header {
            background-color: #28a745;
        }
        .card-info .card-header {
            background-color: #17a2b8;
        }
        .card-warning .card-header {
            background-color: #ffc107;
            color: #1f2d3d;
        }
        .card-header {
            color: white;
        }
        .progress {
            height: 7px;
            margin-top: 5px;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            margin-bottom: 1rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cores para gráficos
            const chartColors = [
                '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', 
                '#e74c3c', '#34495e', '#d35400', '#27ae60', '#8e44ad'
            ];
            
            // Gráfico de Usuários por Role (Polar)
            const userRoleCtx = document.getElementById('usersByRoleChart').getContext('2d');
            new Chart(userRoleCtx, {
                type: 'polarArea',
                data: {
                    labels: @json($roleLabels),
                    datasets: [{
                        data: @json($roleData),
                        backgroundColor: chartColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            
            // Gráfico de Status dos Self Checkouts (Donut)
            const selfStatusCtx = document.getElementById('selfsByStatusChart').getContext('2d');
            new Chart(selfStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ativos', 'Inativos'],
                    datasets: [{
                        data: [{{ $activeSelfs }}, {{ $inactiveSelfs }}],
                        backgroundColor: ['#28a745', '#dc3545'],
                        hoverBackgroundColor: ['#218838', '#c82333'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%'
                }
            });
            
            // Gráfico de Self Checkouts por Unidade (Line)
            const selfUnitCtx = document.getElementById('selfsByUnitChart').getContext('2d');
            new Chart(selfUnitCtx, {
                type: 'line',
                data: {
                    labels: @json($selfsUnitLabels),
                    datasets: [{
                        label: 'Self Checkouts',
                        data: @json($selfsUnitData),
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderColor: '#28a745',
                        borderWidth: 2,
                        pointBackgroundColor: '#28a745',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
            
            // Gráfico de Usuários por Unidade (Horizontal Bar)
            const userUnitCtx = document.getElementById('usersByUnitChart').getContext('2d');
            new Chart(userUnitCtx, {
                type: 'bar',
                data: {
                    labels: @json($userUnitLabels),
                    datasets: [{
                        label: 'Usuários',
                        data: @json($userUnitData),
                        backgroundColor: '#ffc107',
                        borderColor: '#ffc107',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        });
    </script>
@stop