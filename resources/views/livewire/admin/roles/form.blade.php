@section('title', $title)
    
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mx-auto" style="font-weight: bold;">{{ $title }}</h1>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Cargos/Funções</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
            </ol>
        </nav>
    </div>
@stop
    
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informações do Cargo/Função</h3>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="save">
            <div class="form-group">
                <label for="rol_name">Nome do Cargo/Função <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('rol_name') is-invalid @enderror" 
                       id="rol_name" wire:model.lazy="rol_name" 
                       placeholder="Digite o nome do cargo/função" required>
                @error('rol_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mt-4 text-right">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" 
                        class="btn btn-primary" 
                        wire:loading.attr="disabled" 
                        wire:target="save">
                    <span wire:loading.remove wire:target="save">
                        <i class="fas fa-save"></i> Salvar
                    </span>
                    <span wire:loading wire:target="save">
                        <i class="fas fa-spinner fa-spin"></i> Salvando...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@if($isEdit)
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Usuários associados com o Cargo/Função</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Username</th>
                        <th>Unidade</th>
                        <th>Data Criação</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $user)
                    <tr>
                        <td>{{ $user->use_id }}</td>
                        <td>{{ $user->use_name }}</td>
                        <td>{{ $user->use_username }}</td>
                        <td>
                            <span class="text-muted">
                                @php
                                    $unidadeInfo = "";
                                    $unidades = $user->unidades;
                                    if ($unidades && $unidades->count() > 0) {
                                        $primeiraUnidade = $unidades->first();
                                        $unidadeInfo = $primeiraUnidade->uni_codigo . ' - ' . $primeiraUnidade->tipoUnidade->tip_nome;
                                    } else {
                                        $unidadeInfo = "Não definida";
                                    }
                                    echo $unidadeInfo;
                                @endphp
                            </span>
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            @if($user->use_active)
                            <span class="badge bg-success">Ativo</span>
                            @else
                            <span class="badge bg-danger">Inativo</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Nenhum usuário encontrado para este cargo/função</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
    
@section('js')
<script>
    window.addEventListener('toastr:success', event => {
        toastr.success(event.detail.message);
    });
    
    window.addEventListener('toastr:error', event => {
        toastr.error(event.detail.message);
    });
</script>
@stop
    
@section('css')
<style>
    .card-title {
        font-weight: bold;
    }
    .form-group label {
        font-weight: bold;
    }
    .text-danger {
        color: #dc3545!important;
    }
    .badge-success, .bg-success {
        background-color: #28a745;
        color: white;
    }
    .badge-danger, .bg-danger {
        background-color: #dc3545;
        color: white;
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
    }
</style>
@stop