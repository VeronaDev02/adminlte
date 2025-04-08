@extends('adminlte::page')

@section('title', 'Usuários')

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

@section('content')
    <div class="row mb-3">
        <div class="col-12 text-right">
            <a href="{{ route('users.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Adicionar
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-primary">
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
                            <th class="sortable" width="7%">ID <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="15%">Nome <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="15%">Username <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="15%">Email <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="15%">Unidades <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="10%">Role <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="8%">Status <i class="fas fa-sort"></i></th>
                            <th class="text-center" width="15%">Ações</th>
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
                                    // Buscar todas as unidades associadas ao usuário
                                    $unidadeCodigos = \App\Models\Unit::where('unit_use_id', $user->use_id)
                                        ->join('unidade', 'unidade.uni_id', '=', 'units.unit_uni_id')
                                        ->pluck('unidade.uni_codigo')
                                        ->implode(', ');
                                @endphp
                                {{ $unidadeCodigos ?: 'Sem Unidade' }}
                            </td>
                            <td>{{ $user->role ? $user->role->rol_name : 'Sem Role' }}</td>
                            <td>
                                <span class="badge bg-{{ $user->use_active ? 'success' : 'secondary' }}">
                                    {{ $user->use_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('users.edit', $user->use_id) }}" class="btn btn-xs btn-default text-primary" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="POST" action="{{ route('users.destroy', $user->use_id) }}" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-xs btn-default toggle-status {{ $user->use_active ? 'text-success' : 'text-secondary' }}" 
                                        data-url="{{ route('users.toggle-status', $user->use_id) }}" 
                                        data-user-id="{{ $user->use_id }}" 
                                        title="{{ $user->use_active ? 'Desativar' : 'Ativar' }}">
                                    <i class="fas fa-toggle-{{ $user->use_active ? 'on' : 'off' }}"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Nenhum usuário encontrado</td>
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
        .bg-success {
            background-color: #28a745!important;
        }
        .bg-secondary {
            background-color: #6c757d!important;
        }
        .badge {
            display: inline-block;
            padding: .25em .4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            color: white;
        }
    </style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Busca dinâmica
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var value = this.value.toLowerCase();
            var rows = document.querySelectorAll('#dataTable tbody tr');
            
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(value) > -1 ? '' : 'none';
            });
        });
        
        // Ordenação
        document.querySelectorAll('.sortable').forEach(function(header) {
            header.addEventListener('click', function() {
                var table = this.closest('table');
                var index = Array.from(this.parentNode.children).indexOf(this);
                var asc = this.hasAttribute('data-asc') ? !JSON.parse(this.getAttribute('data-asc')) : true;
                this.setAttribute('data-asc', asc);
                
                var rows = Array.from(table.querySelectorAll('tbody tr')).sort(function(a, b) {
                    var valA = a.children[index].textContent.trim();
                    var valB = b.children[index].textContent.trim();
                    
                    if (!isNaN(valA) && !isNaN(valB)) {
                        return asc ? valA - valB : valB - valA;
                    } else {
                        return asc ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    }
                });
                
                var tbody = table.querySelector('tbody');
                rows.forEach(function(row) {
                    tbody.appendChild(row);
                });
            });
        });

        // Toggle status com AJAX
        document.querySelectorAll('.toggle-status').forEach(function(button) {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const url = this.getAttribute('data-url');
                const icon = this.querySelector('i');
                const statusCell = this.closest('tr').querySelector('td:nth-child(7) span');
                
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
                    // Toggle button visual state
                    if (data.status) {
                        this.classList.remove('text-secondary');
                        this.classList.add('text-success');
                        icon.classList.remove('fa-toggle-off');
                        icon.classList.add('fa-toggle-on');
                        this.setAttribute('title', 'Desativar');
                        
                        // Update status badge
                        statusCell.classList.remove('bg-secondary');
                        statusCell.classList.add('bg-success');
                        statusCell.textContent = 'Ativo';
                    } else {
                        this.classList.remove('text-success');
                        this.classList.add('text-secondary');
                        icon.classList.remove('fa-toggle-on');
                        icon.classList.add('fa-toggle-off');
                        this.setAttribute('title', 'Ativar');
                        
                        // Update status badge
                        statusCell.classList.remove('bg-success');
                        statusCell.classList.add('bg-secondary');
                        statusCell.textContent = 'Inativo';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao alterar o status do usuário');
                });
            });
        });
    });
</script>
@stop