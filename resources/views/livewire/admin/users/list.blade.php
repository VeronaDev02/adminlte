<div>
    @section('title', '- Usuários')
    
    @section('content_header')
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mx-auto" style="font-weight: bold;">Usuários</h1>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Usuários</li>
                </ol>
            </nav>
        </div>
    @stop
    
    <div class="row mb-3">
        <div class="col-12 text-right">
            <a href="{{ route('users.create') }}" class="btn btn-success">
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
                            <th wire:click="sortBy('use_id')" class="sortable" width="7%">
                                ID 
                                @if ($sortField === 'use_id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('use_name')" class="sortable" width="20%">
                                Nome
                                @if ($sortField === 'use_name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('use_username')" class="sortable" width="15%">
                                Usuário
                                @if ($sortField === 'use_username')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('use_email')" class="sortable" width="20%">
                                Email
                                @if ($sortField === 'use_email')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </th>
                            <th class="sortable" width="15%">
                                Unidades
                            </th>
                            <th class="sortable" width="10%">
                                Status
                            </th>
                            <th class="text-center" width="13%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->use_id }}</td>
                            <td>{{ $user->use_name }}</td>
                            <td>{{ $user->use_username }}</td>
                            <td>{{ $user->use_email }}</td>
                            <td>
                                @php
                                    $unidadeCodigos = $user->unidades()->pluck('uni_codigo')->implode(', ');
                                @endphp
                                {{ $unidadeCodigos ?: 'Sem Unidade' }}
                            </td>
                            <td>
                                <span class="badge {{ $user->use_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $user->use_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('users.edit', $user->use_id) }}" class="btn btn-xs btn-default text-primary" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button wire:click="confirmDelete({{ $user->use_id }})" class="btn btn-xs btn-default text-danger" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button wire:click="toggleStatus({{ $user->use_id }})" 
                                        class="btn btn-xs btn-default {{ $user->use_active ? 'text-success' : 'text-secondary' }}" 
                                        title="{{ $user->use_active ? 'Desativar' : 'Ativar' }}">
                                    <i class="fas fa-toggle-{{ $user->use_active ? 'on' : 'off' }}"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhum usuário encontrado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-left">
                Mostrando de {{ $users->firstItem() ?? 0 }} até {{ $users->lastItem() ?? 0 }} de {{ $users->total() ?? 0 }} registros
            </div>
            <div class="float-right">
                {{ $users->links() }}
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
                    Tem certeza que deseja excluir este usuário?
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
        window.addEventListener('admin-toastr', event => {
            if (event.detail.type === 'success') {
                toastr.success(event.detail.message);
            } else if (event.detail.type === 'error') {
                toastr.error(event.detail.message);
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        });
        
        // Evento para mostrar o modal de confirmação de exclusão
        window.addEventListener('show-delete-modal', () => {
            $('#deleteModal').modal('show');
        });
        
        // Esconder modal após exclusão
        window.addEventListener('hide-delete-modal', () => {
            $('#deleteModal').modal('hide');
        });
    </script>
    @stop
</div>