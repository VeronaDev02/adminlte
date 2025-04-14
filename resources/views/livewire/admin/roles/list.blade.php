<div>
    @section('title', '- Cargos/Funções')
    
    @section('content_header')
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mx-auto" style="font-weight: bold;">Cargos/Funções</h1>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cargos/Funções</li>
                </ol>
            </nav>
        </div>
    @stop
    
    <div class="row mb-3">
        <div class="col-12 text-right">
            <a href="{{ route('roles.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Adicionar
            </a>
            <button wire:click="$refresh" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12 text-right">
                    <div class="input-group" style="max-width: 300px; float: right;">
                        <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Pesquisar">
                        <div class="input-group-append">
                            <button class="btn btn-default" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('rol_id')" class="sortable" width="10%">
                                ID 
                                @if ($sortField === 'rol_id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('rol_name')" class="sortable" width="40%">
                                Nome
                                @if ($sortField === 'rol_name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th width="25%">Quantidade de Usuários</th>
                            <th wire:click="sortBy('created_at')" class="sortable" width="15%">
                                Data Criação
                                @if ($sortField === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th class="text-center" width="10%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->rol_id }}</td>
                            <td>{{ $role->rol_name }}</td>
                            <td>{{ $role->users->count() }}</td>
                            <td>{{ $role->created_at ? $role->created_at->format('d/m/Y') : '' }}</td>
                            <td class="text-center">
                                <a href="{{ route('roles.edit', $role->rol_id) }}" class="btn btn-xs btn-default text-primary" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $role->rol_id }})" class="btn btn-xs btn-default text-danger" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhum cargo/função encontrado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-left">
                Mostrando de {{ $roles->firstItem() ?? 0 }} até {{ $roles->lastItem() ?? 0 }} de {{ $roles->total() ?? 0 }} registros
            </div>
            <div class="float-right">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">
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
    
    @section('css')
    <style>
        .sortable {
            cursor: pointer;
            white-space: nowrap;
        }
        .sortable i {
            margin-left: 5px;
            font-size: 0.8rem;
        }
        .card-footer {
            padding: 0.75rem 1.25rem;
        }
        .table {
            table-layout: fixed;
            width: 100%;
        }
        .table th, .table td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .btn-xs {
            padding: .125rem .25rem;
            font-size: 1.3em;
            line-height: 1.5;
            border-radius: .15rem;
        }
        .btn-success, .btn-primary {
            padding: 20px 31px;
            font-size: 19px;
            border-radius: 8px;
        }
    </style>
    @stop
    
    @section('js')
    <script>
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
    </script>
    @stop
</div>