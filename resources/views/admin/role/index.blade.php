@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('title', 'Funções')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6"></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Funções</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card m-0">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Funções</h3>
                <div class="col-md-1">
                    <a href="{{ route('admin.role.create') }}" title="Adicionar função" button type="button" class="btn btn-success" style="width: 6rem;">
                        <i class="fa fa-plus"></i>
                        <h6>Adicionar</h6>
                    </a>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.role.index') }}" title="Atualizar funções" button type="button" class="btn btn-info" style="width: 6rem;">
                        <i class="fa fa-sync-alt"></i>
                        <h6>Atualizar</h6>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="table-roles" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Quantidade de Usuários</th>
                        <th>Usuários</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->rol_id }}</td>
                            <td>{{ $role->rol_name }}</td>
                            <td>{{ $role->quantidade_users }}</td>
                            <td>
                                @if($role->users->isNotEmpty())
                                    {{ implode(', ', $role->users->pluck('use_username')->toArray()) }}
                                @else
                                    Nenhum usuário associado
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.role.edit', $role->rol_id) }}">
                                        <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </button>
                                    </a>
                                    <form action="{{ route('admin.role.destroy', $role->rol_id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-default delete-btn">
                                            <i class="fa fa-lg fa-fw fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhuma função encontrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <style>
        #table-roles td, #table-roles th {
            font-weight: normal;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            $('#table-roles').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                },
                pageLength: 25,
                order: [[0, 'asc']]
            });

            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Warning!',
                    text: "Deseja mesmo excluir esta função?",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection