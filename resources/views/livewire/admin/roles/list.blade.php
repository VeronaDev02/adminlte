<div>
    <div class="card m-0">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Funções</h3>
                <div class="col-md-1">
                    <a href="{{ route('roles.create') }}" title="Adicionar função" button="" type="button"
                        class="btn btn-success" style="width: 6rem;">
                        <em class="fa fa-plus"></em>
                        <h6>
                            Adicionar
                        </h6>
                    </a>
                </div>
                <div class="col-md-1">
                    <button onclick="window.location.reload();" title="Atualizar função" button="" type="button"
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
                        'ID',
                        'Nome',
                        'Quantidade de Usuários',
                        'Data Criação',
                        ['label' => 'Ações', 'no-export' => true, 'width' => 8],
                    ];
                    $config = [
                        'order' => [['0', 'asc']],
                        'columns' => [['data' => 'rol_id', 'type' => 'num'], null, null, null, ['orderData' => [6, 4]]],
                        'language' => ['url' => '/vendor/datatables/pt-br.json'],
                    ];
                @endphp
                <x-adminlte-datatable id="role_table" :heads="$heads" :config="$config" striped hoverable bordered
                    with-buttons>
                    @foreach ($roles as $role)
                        <tr style="font-weight: normal;">
                            <td style="font-weight: normal;">{{ $role->rol_id }}</td>
                            <td style="font-weight: normal;">{{ $role->rol_name }}</td>
                            <td style="font-weight: normal;">{{ $role->users->count() }}</td>
                            <td style="font-weight: normal;">{{ $role->created_at ? $role->created_at->format('d/m/Y') : '' }}</td>
                            <td class="row mr-0">
                                <a href="{{ route('roles.edit', $role->rol_id) }}">
                                    <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                        <i class="fa fa-lg fa-fw fa-pen"></i>
                                    </button>
                                </a>
                                <button wire:click="confirmDelete({{ $role->rol_id }})" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Excluir">
                                    <i class="fa fa-lg fa-fw fa-trash"></i>
                                </button>
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
                    Tem certeza que deseja excluir este cargo/função?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" wire:click="destroy" class="btn btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Erro -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="errorModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> Erro ao Excluir
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="errorModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
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
    </style>

    <script>
        document.addEventListener('livewire:load', function () {
            window.addEventListener('show-error-modal', (event) => {
                $('#errorModalBody').text(event.detail.message);
                $('#errorModal').modal('show');
            });
            
            window.addEventListener('show-delete-modal', () => {
                $('#deleteModal').modal('show');
            });
            
            window.addEventListener('hide-delete-modal', () => {
                $('#deleteModal').modal('hide');
            });
            
            window.addEventListener('toastr:success', event => {
                toastr.success(event.detail.message);
            });
            
            window.addEventListener('toastr:error', event => {
                toastr.error(event.detail.message);
            });
        });
    </script>
</div>