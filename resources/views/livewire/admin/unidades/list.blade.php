<div>
    @section('title', '- Unidades')
    
    @section('content_header')
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mx-auto" style="font-weight: bold;">Unidades</h1>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Unidades</li>
                </ol>
            </nav>
        </div>
    @stop
    
    <div class="row mb-3">
        <div class="col-12 text-right">
            <a href="{{ route('unidades.create') }}" class="btn btn-success">
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
                            <th wire:click="sortBy('uni_id')" class="sortable" width="10%">
                                ID 
                                @if ($sortField === 'uni_id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('uni_codigo')" class="sortable" width="20%">
                                Código
                                @if ($sortField === 'uni_codigo')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th width="25%">Tipo</th>
                            <th wire:click="sortBy('created_at')" class="sortable" width="20%">
                                Data Criação
                                @if ($sortField === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('updated_at')" class="sortable" width="15%">
                                Última Atualização
                                @if ($sortField === 'updated_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th class="text-center" width="10%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unidades as $unidade)
                        <tr>
                            <td>{{ $unidade->uni_id }}</td>
                            <td>{{ $unidade->uni_codigo }}</td>
                            <td>{{ $unidade->tipoUnidade->tip_nome }}</td>
                            <td>{{ $unidade->created_at ? $unidade->created_at->format('d/m/Y') : '' }}</td>
                            <td>{{ $unidade->updated_at ? $unidade->updated_at->format('d/m/Y') : '' }}</td>
                            <td class="text-center">
                                <a href="{{ route('unidades.edit', $unidade->uni_id) }}" class="btn btn-xs btn-default text-primary" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $unidade->uni_id }})" class="btn btn-xs btn-default text-danger" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma unidade encontrada</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-left">
                Mostrando de {{ $unidades->firstItem() ?? 0 }} até {{ $unidades->lastItem() ?? 0 }} de {{ $unidades->total() ?? 0 }} registros
            </div>
            <div class="float-right">
                {{ $unidades->links() }}
            </div>
        </div>
    </div>
    
    <!-- Modal de confirmação de exclusão -->
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
                    Tem certeza que deseja excluir esta unidade?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" wire:click="destroy" class="btn btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de erro -->
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
    
    @section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
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
        .pagination {
            margin-bottom: 0;
        }
        
        .table td .btn.btn-xs,
        .table td .btn-group .btn,
        .btn.btn-xs {
            padding: 0.5rem 0.75rem !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            border-radius: 0.25rem !important;
            min-width: 40px;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .table td .btn.btn-xs i,
        .table td .btn-group .btn i,
        .btn.btn-xs i {
            font-size: 1rem !important;
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