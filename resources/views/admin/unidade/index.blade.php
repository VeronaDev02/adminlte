@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Toastr', false)
@section('title', 'Unidades')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Unidades</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card m-0">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Unidades</h3>
                <div class="col-md-1">
                    <a href="{{ route('admin.unidade.create') }}" title="Adicionar unidade" button="" type="button"
                        class="btn btn-success" style="width: 6rem;">
                        <em class="fa fa-plus"></em>
                        <h6>
                            Adicionar
                        </h6>
                    </a>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.unidade.index') }}" title="Atualizar unidade" button="" type="button"
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
                    'Código',
                    'Tipo Unidade',
                    'Nome',
                    'Usuários',
                    'Selfs',
                    'Data Criação',
                    ['label' => 'API', 'no-export' => true], 
                    ['label' => 'Ações', 'no-export' => true, 'width' => 8],
                ];
                $config = [
                    'order' => [['0', 'asc']],
                    'columns' => [
                        ['data' => 'uni_id', 'type' => 'num'],
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        ['orderable' => false],
                        ['orderData' => [6, 2]],
                    ],
                    'language' => ['url' => '/vendor/datatables/pt-br.json'],
                ];
            @endphp
            <x-adminlte-datatable id="unidade_table" :heads="$heads" :config="$config" striped hoverable bordered
                with-buttons>
                @foreach ($unidades as $unidade)
                    <tr>
                        <td>{{ $unidade->uni_id }}</td>
                        <td>{{ $unidade->uni_codigo }}</td>
                        <td>{{ $unidade->tipoUnidade->tip_nome }}</td>
                        <td>{{ $unidade->uni_nome }}</td>
                        <td>
                            @if(count($unidade->users) > 0)
                                {{ $unidade->users->pluck('use_username')->implode(', ') }}
                            @else
                                Nenhum usuário
                            @endif
                        </td>
                        <td>
                            @if(count($unidade->selfs) > 0)
                                {{ $unidade->selfs->pluck('sel_name')->implode(', ') }}
                            @else
                                Nenhum SelfCheckout
                            @endif
                        </td>
                        <td>{{ $unidade->created_at ? date('d/m/Y', strtotime($unidade->created_at)) : 'N/A' }}</td>
                        <td class="text-center align-middle">
                            @if(!empty($unidade->uni_api) && !empty($unidade->uni_api_login) && !empty($unidade->uni_api_password))
                                @livewire('admin.unidades.toggle-api-status', ['unidade' => $unidade], key('api-toggle-'.$unidade->uni_id))
                            @else
                                <span class="badge badge-secondary">Não configurado</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.unidade.edit', $unidade->uni_id) }}" class="btn btn-xs btn-default mr-1">
                                    <i class="fa fa-lg fa-fw fa-pen text-primary"></i>
                                </a>
                                <form action="{{ route('admin.unidade.destroy', $unidade->uni_id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default delete-btn">
                                        <i class="fa fa-lg fa-fw fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </div>
    </div>
@stop

@section('css')
    <style>
        #unidade_table td {
            font-weight: normal !important;
        }
        
        #unidade_table th {
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
        
        #unidade_table button,
        #unidade_table .btn {
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
        $(function() {
            
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Warning!',
                    text: "Não há como reverter esta ação!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection