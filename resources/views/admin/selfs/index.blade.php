@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Toastr', true)
@section('title', 'SelfCheckouts')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">SelfCheckouts</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card m-0">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">SelfCheckouts</h3>
                <div class="col-md-1">
                    <a href="{{ route('admin.selfs.create') }}" title="Adicionar SelfCheckout" button="" type="button"
                        class="btn btn-success" style="width: 6rem;">
                        <em class="fa fa-plus"></em>
                        <h6>
                            Adicionar
                        </h6>
                    </a>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.selfs.index') }}" title="Atualizar SelfCheckout" button="" type="button"
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
                    'Código PDV',
                    'IP PDV',
                    'IP DVR',
                    'Unidade',
                    'Data Criação',
                    ['label' => 'Status', 'no-export' => true],
                    ['label' => 'Ações', 'no-export' => true, 'width' => 8],
                ];
                $config = [
                    'order' => [['0', 'asc']],
                    'columns' => [
                        ['data' => 'sel_id', 'type' => 'num'],
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        ['orderData' => [7, 3]],
                        ['orderData' => [7, 3]],
                    ],
                    'language' => ['url' => '/vendor/datatables/pt-br.json'],
                ];
            @endphp
            <x-adminlte-datatable id="selfs_table" :heads="$heads" :config="$config" striped hoverable bordered
                with-buttons>
                @foreach ($selfs as $self)
                    <tr>
                        <td>{{ $self->sel_id }}</td>
                        <td>{{ $self->sel_name }}</td>
                        <td>{{ $self->sel_pdv_codigo }}</td>
                        <td>{{ $self->sel_pdv_ip }}</td>
                        <td>{{ $self->sel_dvr_ip }}</td>
                        <td>{{ $self->unidade->uni_codigo . " - " . 
                            $self->unidade->uni_nome . " - " . 
                            $self->unidade->tipoUnidade->tip_nome }}
                        </td>
                        <td>{{ $self->created_at ? date('d/m/Y', strtotime($self->created_at)) : 'N/A' }}</td>
                        <td class="text-center align-middle">
                            <livewire:admin.selfs.toggle-on-selfs :selfs="$self" :wire:key="'toggle-status-'.$self->sel_id" />
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ route('admin.selfs.edit', $self->sel_id) }}">
                                <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </button>
                            </a>
                            <form action="{{ route('admin.selfs.destroy', $self->sel_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-default delete-btn">
                                    <i class="fa fa-lg fa-fw fa-trash text-danger"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </div>
    </div>
@stop

@section('css')
    <style>
        #selfs_table td {
            font-weight: normal !important;
        }
        
        #selfs_table th {
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
        
        #selfs_table button,
        #selfs_table .btn {
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
        // @if(session('success'))
        //     toastr.success("{{ session('success') }}");
        // @endif

        // @if(session('error'))
        //     toastr.error("{{ session('error') }}");
        // @endif
        
        Livewire.on('updateStatusSelfs', (event) => {
            if (event.status) {
                success.fire('Sucesso!', event.message, 'success');
            } else {
                error.fire('Erro!', event.message, 'error');
            }
        });

        // Confirmação de exclusão
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Tem certeza?',
                text: "Você não poderá reverter esta ação!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).unbind('submit').submit();
                }
            });
        });
    </script>
@endsection