<div>
    <x-card title="Unidades" class="m-0" :has-tools="true">
        <x-slot name="tools">
            <div class="d-flex justify-content-end">
                <a href="{{ route('unidades.create') }}" class="btn btn-success mr-2">
                    <i class="fa fa-plus mr-1"></i> Adicionar
                </a>
                <button onclick="window.location.reload();" class="btn btn-info">
                    <i class="fa fa-sync-alt mr-1"></i> Atualizar
                </button>
            </div>
        </x-slot>

        <div wire:ignore>
            @php
                $heads = [
                    'ID',
                    'Código',
                    'Tipo',
                    'Data Criação',
                    'Última Atualização',
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
                        ['orderData' => [6, 5]]
                    ],
                    'language' => ['url' => '/vendor/datatables/pt-br.json'],
                ];
            @endphp
            <x-adminlte-datatable id="unidades_table" :heads="$heads" :config="$config" striped hoverable bordered
                with-buttons>
                @foreach ($unidades as $unidade)
                    <tr style="font-weight: normal;">
                        <td style="font-weight: normal;">{{ $unidade->uni_id }}</td>
                        <td style="font-weight: normal;">{{ $unidade->uni_codigo }}</td>
                        <td style="font-weight: normal;">{{ $unidade->tipoUnidade->tip_nome }}</td>
                        <td style="font-weight: normal;">{{ $unidade->created_at ? $unidade->created_at->format('d/m/Y') : '' }}</td>
                        <td style="font-weight: normal;">{{ $unidade->updated_at ? $unidade->updated_at->format('d/m/Y') : '' }}</td>
                        <td class="text-right d-flex justify-content-end">
                            <a href="{{ route('unidades.edit', $unidade->uni_id) }}" class="btn btn-xs btn-default text-primary mx-1 shadow mr-1">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $unidade->uni_id }})" class="btn btn-xs btn-default text-danger mx-1 shadow">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </div>
    </x-card>

    <x-modal 
        id="deleteModal" 
        title="Confirmação" 
        type="default">
        <div>
            Tem certeza que deseja excluir esta unidade?
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fa fa-times mr-1"></i> Cancelar
            </button>
            <button wire:click="destroy" class="btn btn-danger ml-2">
                <i class="fa fa-trash mr-1"></i> Excluir
            </button>
        </x-slot>
    </x-modal>

    <x-modal 
        id="errorModal" 
        title="Erro ao Excluir" 
        type="danger">
        <div id="errorModalBody">
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fa fa-times mr-1"></i> Fechar
            </button>
        </x-slot>
    </x-modal>

    <style>
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
        
        div.dataTables_wrapper div.dataTables_info {
            font-weight: normal !important;
        }
        
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