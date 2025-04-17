<div>
    @section('content_header')
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item" style="font-weight: normal;"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" style="font-weight: normal;">SelfCheckouts</li>
                </ol>
            </div>
        </div>
    @stop
    <div class="card m-0">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">SelfCheckouts</h3>
                <div class="col-md-1">
                    <a href="{{ route('selfs.create') }}" title="Adicionar SelfCheckout" button="" type="button"
                        class="btn btn-success" style="width: 6rem;">
                        <em class="fa fa-plus"></em>
                        <h6>
                            Adicionar
                        </h6>
                    </a>
                </div>
                <div class="col-md-1">
                    <button onclick="window.location.reload();" title="Atualizar SelfCheckouts" button="" type="button"
                        class="btn btn-info" style="width: 6rem;">
                        <em class="fa fa-sync-alt"></em>
                        <h6>
                            Atualizar
                        </h6>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div wire:ignore>
                @php
                    $heads = [
                        ['label' => 'ID', 'width' => 5],
                        ['label' => 'Nome', 'width' => 25],
                        ['label' => 'Unidade', 'width' => 25],
                        ['label' => 'Status', 'width' => 15],
                        ['label' => 'Ações', 'no-export' => true, 'width' => 15],
                    ];
                    $config = [
                        'order' => [['0', 'asc']],
                        'columns' => [
                            ['data' => 'sel_id', 'type' => 'num'], 
                            null, 
                            null, 
                            null, 
                            ['orderable' => false]
                        ],
                        'language' => ['url' => '/vendor/datatables/pt-br.json'],
                    ];
                @endphp
                <x-adminlte-datatable id="selfs_table" :heads="$heads" :config="$config" striped hoverable bordered
                    with-buttons>
                    @foreach ($selfs as $self)
                        <tr style="font-weight: normal;">
                            <td style="font-weight: normal;">{{ $self->sel_id }}</td>
                            <td style="font-weight: normal;">{{ $self->sel_name }}</td>
                            <td style="font-weight: normal;">{{ $self->unidade ? $self->unidade->uni_codigo . ' - ' . $self->unidade->nome : 'Sem Unidade' }}</td>
                            <td style="font-weight: normal;" class="text-center">
                                <span class="badge {{ $self->sel_status ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $self->sel_status ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td style="font-weight: normal;" class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('selfs.edit', $self->sel_id) }}" title="Editar">
                                        <button class="btn btn-xs btn-default text-primary mx-1 shadow">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </button>
                                    </a>
                                    <button wire:click="confirmDelete({{ $self->sel_id }})" title="Excluir" class="btn btn-xs btn-default text-danger mx-1 shadow">
                                        <i class="fa fa-lg fa-fw fa-trash"></i>
                                    </button>
                                    <button wire:click="toggleStatus({{ $self->sel_id }})" title="{{ $self->sel_status ? 'Desativar' : 'Ativar' }}" class="btn btn-xs btn-default text-primary mx-1 shadow">
                                        @if ($self->sel_status)
                                            <i class="fas fa-lg fa-fw fa-toggle-on" style="color: #00ff33;"></i>
                                        @else
                                            <i class="fas fa-lg fa-fw fa-toggle-off" style="color: #ff0000;"></i>
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmação</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir este SelfCheckout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" wire:click="destroy" class="btn btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Remove negrito de todos os elementos de paginação */
        .dataTables_info,
        .dataTables_paginate,
        .paginate_button,
        .page-item,
        .page-link,
        .pagination-footer span,
        .pagination,
        .dataTables_length,
        .dataTables_filter {
            font-weight: normal !important;
        }
        
        /* Especificamente para o texto "Mostrando de X até Y..." */
        div.dataTables_wrapper div.dataTables_info {
            font-weight: normal !important;
        }
        
        /* Para atingir elementos gerados pelo Livewire */
        nav[aria-label="Pagination Navigation"] span,
        nav[aria-label="Pagination Navigation"] a {
            font-weight: normal !important;
        }
        
        /* Alinhar botões na mesma linha */
        .btn-group {
            display: inline-flex;
            align-items: center;
        }
    </style>

    <script>
        document.addEventListener('livewire:load', function () {
            window.addEventListener('show-delete-modal', () => {
                $('#deleteModal').modal('show');
            });
            
            window.addEventListener('hide-delete-modal', () => {
                $('#deleteModal').modal('hide');
            });
            
            window.addEventListener('admin-toastr', event => {
                toastr[event.detail.type](event.detail.message);
            });
        });
    </script>
</div>