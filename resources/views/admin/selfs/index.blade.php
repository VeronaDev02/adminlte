@extends('adminlte::page')
@component('components.alert.sweet-alert')

@endcomponent
@section('title', '- IPs & RTSPs')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">SelfCheckouts</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">SelfCheckouts</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col-12 text-right">
            <a href="{{ route('selfs.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Adicionar
            </a>
            <a href="{{ route('selfs.index') }}" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i> Atualizar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12 text-right">
                    <div class="input-group" style="max-width: 300px; float: right;">
                        <input type="text" class="form-control" placeholder="Pesquisar" id="searchInput">
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
                            <th class="sortable" width="5%">ID <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="6%">Nome <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="10%">IP <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="45%">RTSP URL <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="9%">Unidade <i class="fas fa-sort"></i></th>
                            <th class="text-center" width="13%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selfs as $self)
                        <tr>
                            <td>{{ $self->sel_id }}</td>
                            <td>{{ $self->sel_name }}</td>
                            <td>{{ $self->sel_pdv_ip }}</td>
                            <td>{{ $self->sel_rtsp_url }}</td>
                            <td>{{ $self->unidade ? $self->unidade->uni_codigo : 'Sem Unidade' }}</td>
                            <td class="text-center">
                                <a href="{{ route('selfs.edit', $self->sel_id) }}" class="btn btn-xs btn-default text-primary" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="POST" action="{{ route('selfs.destroy', $self->sel_id) }}" class="d-inline" onsubmit="return ">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        class="btn btn-xs btn-default text-danger btn-delete" 
                                        data-id="{{ $self->sel_id }}"
                                        data-route="{{ route('selfs.destroy', $self->sel_id) }}"
                                        title="Excluir"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-xs btn-default toggle-status {{ $self->sel_status ? 'text-success' : 'text-secondary' }}" 
                                        data-url="{{ route('selfs.toggle-status', $self->sel_id) }}" 
                                        data-self-id="{{ $self->sel_id }}" 
                                        title="{{ $self->sel_status ? 'Desativar' : 'Ativar' }}">
                                    <i class="fas fa-toggle-{{ $self->sel_status ? 'on' : 'off' }}"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhum SelfCheckou encontrado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-left">
                Mostrando de {{ $selfs->firstItem() ?? 0 }} até {{ $selfs->lastItem() ?? 0 }} de {{ $selfs->total() ?? 0 }} registros
            </div>
            <div class="float-right">
                {{ $selfs->links() }}
            </div>
        </div>
    </div>
@stop

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gerenciamento de Mensagens de Redirecionamento para quando editarmos um Selfcheckout não ter que esperar a mensagem.
        const redirectMessage = localStorage.getItem('redirectMessage');
        const redirectMessageType = localStorage.getItem('redirectMessageType');
        
        if (redirectMessage) {
            const method = redirectMessageType === 'success' ? mostrarSucesso : mostrarErro;
            method(redirectMessage, redirectMessageType === 'success' ? 'Sucesso' : 'Erro');
            
            localStorage.removeItem('redirectMessage');
            localStorage.removeItem('redirectMessageType');
        }

        // Configuração dos Eventos
        setupDeleteButtons();
        setupToggleStatusButtons();
        setupSearchInput();
        setupSortableColumns();
    });

    // Configuração dos Botões de excluir
    function setupDeleteButtons() {
        document.querySelectorAll('.btn-delete').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('form');
                const route = this.getAttribute('data-route');

                Swal.fire({
                    title: 'Confirmação',
                    text: 'Tem certeza que deseja excluir este SelfCheckout?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }

    // Configuração dos botões de status, aquele toggle pra selfcheckout ativo/inativo
    function setupToggleStatusButtons() {
        document.querySelectorAll('.toggle-status').forEach(function(button) {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const icon = this.querySelector('i');
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const statusText = data.status ? 'Ativo' : 'Inativo';

                    if (data.status) {
                        this.classList.remove('text-secondary');
                        this.classList.add('text-success');
                        icon.classList.remove('fa-toggle-off');
                        icon.classList.add('fa-toggle-on');
                        this.setAttribute('title', 'Desativar');
                    } else {
                        this.classList.remove('text-success');
                        this.classList.add('text-secondary');
                        icon.classList.remove('fa-toggle-on');
                        icon.classList.add('fa-toggle-off');
                        this.setAttribute('title', 'Ativar');
                    }
                    
                    mostrarSucesso(`O SelfCheckout foi marcado como ${statusText}.`, 'Status alterado!');
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarErro('Erro ao alterar o status do SelfCheckout', 'Erro!');
                });
            });
        });
    }

    // Configuração para fazermos a busca dinâmica
    function setupSearchInput() {
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dataTable tbody tr');
            
            rows.forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
            });
        });
    }

    // Configuração para conseguir ordenar a lista/tabela que temos
    function setupSortableColumns() {
        document.querySelectorAll('.sortable').forEach(function(header) {
            header.addEventListener('click', function() {
                const table = this.closest('table');
                const index = Array.from(this.parentNode.children).indexOf(this);
                const asc = this.hasAttribute('data-asc') ? !JSON.parse(this.getAttribute('data-asc')) : true;
                this.setAttribute('data-asc', asc);
                
                const rows = Array.from(table.querySelectorAll('tbody tr'))
                    .sort((a, b) => {
                        const valA = a.children[index].textContent.trim();
                        const valB = b.children[index].textContent.trim();
                        
                        if (!isNaN(valA) && !isNaN(valB)) {
                            return asc ? valA - valB : valB - valA;
                        } else {
                            return asc ? valA.localeCompare(valB) : valB.localeCompare(valA);
                        }
                    });
                
                const tbody = table.querySelector('tbody');
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    }
</script>
@stop