@section('title', 'Funções')

<div>
    <x-card title="Funções" class="m-0" :has-tools="true">
        <x-slot name="tools">
            <div class="d-flex justify-content-end">
                <a href="{{ route('roles.create') }}" class="btn btn-success mr-2">
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
                        <td class="text-right d-flex justify-content-end">
                            <a href="{{ route('roles.edit', $role->rol_id) }}" class="btn btn-xs btn-default text-primary mx-1 shadow mr-1">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $role->rol_id }})" class="btn btn-xs btn-default text-danger mx-1 shadow">
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
            Tem certeza que deseja excluir este cargo/função?
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
        /* Estilos existentes mantidos */
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