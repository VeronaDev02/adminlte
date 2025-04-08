@extends('adminlte::page')

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
                            <th class="sortable" width="6%">ID <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="6%">Nome <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="10%">IP <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="45%">RTSP URL <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="12%">Unidade <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="8%">Data Criação <i class="fas fa-sort"></i></th>
                            <th class="text-center" width="12%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selfs as $self)
                        <tr>
                            <td>{{ $self->sel_id }}</td>
                            <td>{{ $self->sel_name }}</td>
                            <td>{{ $self->sel_pdv_ip }}</td>
                            <td>{{ $self->sel_rtsp_url }}</td>
                            <td>{{ $self->unidade ? $self->unidade->uni_codigo . ' - ' . $self->unidade->uni_descricao : 'Sem Unidade' }}</td>
                            <td>{{ $self->created_at ? $self->created_at->format('d/m/Y') : '' }}</td>
                            <td class="text-center">
                                <a href="{{ route('selfs.edit', $self->sel_id) }}" class="btn btn-xs btn-default text-primary" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="POST" action="{{ route('selfs.destroy', $self->sel_id) }}" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este PDV?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('selfs.toggle-status', $self->sel_id) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-xs btn-default {{ $self->sel_status ? 'text-success' : 'text-secondary' }}" title="{{ $self->sel_status ? 'Desativar' : 'Ativar' }}">
                                        <i class="fas fa-toggle-{{ $self->sel_status ? 'on' : 'off' }}"></i>
                                    </button>
                                </form>
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
        .btn-success {
            padding: 20px 31px;
            font-size: 19px;
            border-radius: 8px;
        }
        .btn-primary {
            padding: 20px 31px;
            font-size: 19px;
            border-radius: 8px;
        }
    </style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Previous search and sorting scripts remain the same...

        // Prevent toggle status refresh
        document.querySelectorAll('form[action*="toggle-status"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop the form from submitting normally
                
                const button = this.querySelector('button');
                const icon = button.querySelector('i');
                
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Toggle button visual state
                    if (data.status) {
                        button.classList.remove('text-secondary');
                        button.classList.add('text-success');
                        icon.classList.remove('fa-toggle-off');
                        icon.classList.add('fa-toggle-on');
                        button.setAttribute('title', 'Desativar');
                    } else {
                        button.classList.remove('text-success');
                        button.classList.add('text-secondary');
                        icon.classList.remove('fa-toggle-on');
                        icon.classList.add('fa-toggle-off');
                        button.setAttribute('title', 'Ativar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao alterar o status do SelfCheckout');
                });
            });
        });
    });
</script>
@stop