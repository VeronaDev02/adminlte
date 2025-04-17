<div>
    @section('title', $title)
    
    @section('content_header')
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Funções</a></li>
                    <li class="breadcrumb-item active">{{ $title }}</li>
                </ol>
            </div>
        </div>
    @stop
        
    <div class="card">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">{{ $title }}</h3>
            </div>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="rol_name">Nome do Cargo/Função</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('rol_name') is-invalid @enderror" 
                                   id="rol_name" wire:model.lazy="rol_name" 
                                   placeholder="Digite o nome do cargo/função" required>
                            <div class="input-group-append">
                                <button id="enviar" type="submit" class="btn btn-primary ml-2 d-flex align-items-center" style="height: 38px;">
                                    @if ($isEdit)
                                        <i class="fa fa-pen mr-1"></i>
                                        Atualizar
                                    @else
                                        <i class="fa fa-plus mr-1"></i>
                                        Cadastrar
                                    @endif
                                </button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary ml-2 d-flex align-items-center" style="height: 38px;">
                                    <i class="fa fa-times mr-1"></i>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                        @error('rol_name')
                            <span class="invalid-feedback" style="display: unset;" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($isEdit)
    <div class="card mt-4">
        <div class="card-header">
            <div class="row">
                <h3 class="col-md-10">Usuários associados com o Cargo/Função</h3>
            </div>
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
                        <tr style="font-weight: normal;">
                            <td style="font-weight: normal;">{{ $user->use_id }}</td>
                            <td style="font-weight: normal;">{{ $user->use_name }}</td>
                            <td style="font-weight: normal;">{{ $user->use_username }}</td>
                            <td style="font-weight: normal;">
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
                            </td>
                            <td style="font-weight: normal;">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                            <td style="font-weight: normal;">
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
        
    <script>
        document.addEventListener('livewire:load', function () {
            window.addEventListener('toastr:success', event => {
                toastr.success(event.detail.message);
            });
            
            window.addEventListener('toastr:error', event => {
                toastr.error(event.detail.message);
            });
        });
    </script>
</div>