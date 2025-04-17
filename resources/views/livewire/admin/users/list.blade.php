<div>
    <div class="card m-0">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Usuários</h3>
                <div class="col-md-1">
                    <a href="{{ route('users.create') }}" title="Adicionar usuário" button="" type="button"
                        class="btn btn-success" style="width: 6rem;">
                        <em class="fa fa-plus"></em>
                        <h6>
                            Adicionar
                        </h6>
                    </a>
                </div>
                <div class="col-md-1">
                    <button wire:click="$refresh" title="Atualizar usuários" button="" type="button"
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
                        ['label' => 'Usuário', 'width' => 15],
                        ['label' => 'Unidades', 'width' => 10],
                        ['label' => 'Data Criação', 'width' => 15],
                        ['label' => 'Nova senha', 'width' => 15],
                        ['label' => 'Ações', 'no-export' => true, 'width' => 15],
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
                            ['orderable' => false]
                        ],
                        'language' => ['url' => '/vendor/datatables/pt-br.json'],
                    ];
                @endphp
                <x-adminlte-datatable id="users_table" :heads="$heads" :config="$config" striped hoverable bordered
                    with-buttons>
                    @foreach ($users as $user)
                        <tr style="font-weight: normal;">
                            <td style="font-weight: normal;">{{ $user->use_id }}</td>
                            <td style="font-weight: normal;">{{ $user->use_name }}</td>
                            <td style="font-weight: normal;">{{ $user->use_username }}</td>
                            <td style="font-weight: normal;">
                                @foreach ($user->unidades_codigo as $codigo)
                                    {{ $codigo }}
                                    @if (!$loop->last)
                                        -
                                    @endif
                                @endforeach
                            </td>
                            <td style="font-weight: normal;">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '' }}</td>
                            <td style="font-weight: normal;" class="text-center">
                                <button wire:click="resetPassword({{ $user->use_id }})" class="btn btn-primary btn-sm">
                                    Redefinir
                                </button>
                            </td>
                            <td style="font-weight: normal;" class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('users.edit', $user->use_id) }}" title="Editar">
                                        <button class="btn btn-xs btn-default text-primary mx-1 shadow">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </button>
                                    </a>
                                    <button wire:click="confirmDelete({{ $user->use_id }})" title="Excluir" class="btn btn-xs btn-default text-danger mx-1 shadow">
                                        <i class="fa fa-lg fa-fw fa-trash"></i>
                                    </button>
                                    <button wire:click="toggleStatus({{ $user->use_id }})" title="{{ $user->use_active ? 'Desativar' : 'Ativar' }}" class="btn btn-xs btn-default text-primary mx-1 shadow">
                                        @if ($user->use_active)
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
                    Tem certeza que deseja excluir este usuário?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" wire:click="destroy" class="btn btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmação de Reset de Senha -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Resetar senha</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p>Você tem certeza que deseja resetar a senha do usuário</p>
                    <h4><strong>{{ $userToReset ? $userToReset->use_name : '' }}</strong></h4>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" wire:click="doResetPassword" class="btn btn-primary btn-block">Resetar</button>
                    <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Senha Redefinida -->
    <div class="modal fade" id="passwordResetedModal" tabindex="-1" role="dialog" aria-labelledby="passwordResetedModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordResetedModalLabel">Resetar senha</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h4>Usuário: <strong>{{ $userToReset ? $userToReset->use_username : '' }}</strong></h4>
                    <h4>Nova senha: <strong>senha123</strong></h4>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" wire:click="resetAnotherPassword" class="btn btn-primary btn-block">Outra senha</button>
                    <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Voltar</button>
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
        
        /* Ajustar tamanho do botão de redefinir senha */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
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
            
            window.addEventListener('show-reset-password-modal', () => {
                $('#resetPasswordModal').modal('show');
            });
            
            window.addEventListener('hide-reset-password-modal', () => {
                $('#resetPasswordModal').modal('hide');
            });
            
            window.addEventListener('show-password-reseted-modal', () => {
                $('#passwordResetedModal').modal('show');
            });
            
            window.addEventListener('hide-password-reseted-modal', () => {
                $('#passwordResetedModal').modal('hide');
            });
            
            window.addEventListener('admin-toastr', event => {
                toastr[event.detail.type](event.detail.message);
            });
        });
    </script>
</div>