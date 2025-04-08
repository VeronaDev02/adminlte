@extends('adminlte::page')

@section('title', 'Unidades')

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

@section('content')
    <div class="row mb-3">
        <div class="col-12 text-right">
            <a href="{{ route('unidades.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Adicionar
            </a>
            <a href="{{ route('unidades.index') }}" class="btn btn-primary">
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
                            <th class="sortable" width="10%">ID <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="20%">Código <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="35%">Nome <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="35%">Cidade <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="20%">UF <i class="fas fa-sort"></i></th>
                            <th class="sortable" width="20%">Data Criação <i class="fas fa-sort"></i></th>
                            <th class="text-center" width="15%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unidades as $unidade)
                        <tr>
                            <td>{{ $unidade->uni_id }}</td>
                            <td>{{ $unidade->uni_codigo }}</td>
                            <td>{{ $unidade->uni_descricao }}</td>
                            <td>{{ $unidade->uni_cidade }}</td>
                            <td>{{ $unidade->uni_uf }}</td>
                            <td>{{ $unidade->created_at ? $unidade->created_at->format('d/m/Y') : '' }}</td>
                            <td class="text-center">
                                <a href="{{ route('unidades.edit', $unidade->uni_id) }}" class="btn btn-xs btn-default text-primary" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="POST" action="{{ route('unidades.destroy', $unidade->uni_id) }}" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta unidade? Esta ação excluirá todos os usuários e selfs associados.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-default text-danger" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhuma unidade encontrada</td>
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
    });
</script>
@stop