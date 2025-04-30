@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Toastr', true)
@section('title', 'Usuários')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Usuários</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card m-0">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Usuários</h3>
                <div class="col-md-1">
                    <a href="{{ route('admin.user.create') }}" title="Adicionar usuário" button="" type="button"
                        class="btn btn-success" style="width: 6rem;">
                        <em class="fa fa-plus"></em>
                        <h6>
                            Adicionar
                        </h6>
                    </a>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.user.index') }}" title="Atualizar usuário" button="" type="button"
                        class="btn btn-info" style="width: 6rem;">
                        <em class="fa fa-sync-alt"></em>
                        <h6>
                            Atualizar
                        </h6>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @php
                $heads = [
                    'ID',
                    'Nome',
                    'Usuário',
                    'Unidades',
                    'Login Ativo',
                    'Data Criação',
                    'Nova senha',
                    ['label' => 'Ações', 'no-export' => true, 'width' => 8],
                ];
                $config = [
                    'order' => [['0', 'asc']],
                    'columns' => [
                        ['data' => 'use_id', 'type' => 'num'],
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        ['orderData' => [6, 3]],
                    ],
                    'language' => ['url' => '/vendor/datatables/pt-br.json'],
                ];
            @endphp
            <x-adminlte-datatable id="user_table" :heads="$heads" :config="$config" striped hoverable bordered
                with-buttons>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->use_id }}</td>
                        <td>{{ $user->use_name }}</td>
                        <td>{{ $user->use_username }}</td>
                        <td>{{ $user->unidades_formatado }}</td>
                        <td class="row mr-0 d-flex align-items-center">
                            <livewire:admin.user.toggle-on-login-ativo :user="$user" :wire:key="'login-'.$user->use_id" />
                        </td>
                        <td>{{ $user->created_at ? date('d/m/Y', strtotime($user->created_at)) : 'N/A' }}</td>
                        <td>
                            <livewire:admin.user.reset-password :user="$user" :wire:key="'reset-'.$user->use_id" />
                        </td>
                        <td class="row mr-0">
                            <a href="{{ route('admin.user.edit', $user->use_id) }}">
                                <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </button>
                            </a>
                            <livewire:admin.user.toggle-on-user :user="$user" :wire:key="'toggle-'.$user->use_id" />
                        </td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </div>
    </div>
@stop

@section('css')
    <style>
        #user_table td {
            font-weight: normal !important;
        }
        
        #user_table th {
            font-weight: normal !important;
        }
        
        .dataTables_paginate .page-link,
        .dataTables_paginate .paginate_button {
            font-weight: normal !important;
        }
        
        .dataTables_info {
            font-weight: normal !important;
        }
        
        .breadcrumb-item {
            font-weight: normal !important;
        }
        
        #user_table button,
        #user_table .btn {
            font-weight: normal !important;
        }
        
        .dataTables_wrapper .dt-buttons button,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            font-weight: normal !important;
        }
    </style>
@stop

@section('js')
    <script>
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        
        Livewire.on('updateStatusUser', (event) => {
            if (event.status) {
                success.fire('Sucesso!', event.message, 'success');
            } else {
                error.fire('Erro!', event.message, 'error');
            }
        });
        
        Livewire.on('updateStatusLoginAtivo', (event) => {
            if (event.status) {
                success.fire('Sucesso!', event.message, 'success');
            } else {
                error.fire('Erro!', event.message, 'error');
            }
        });
    </script>
@endsection