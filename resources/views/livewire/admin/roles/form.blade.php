@section('title', $title)

@section('content_header')
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Funções', 'url' => route('roles.index')],
        ['label' => $title, 'active' => true],
    ]" />
@stop
    
<x-card :title="$title">
    <form wire:submit.prevent="save">
        <div class="row">
            <x-form-input 
                label="Nome do Cargo/Função" 
                name="rol_name" 
                placeholder="Digite o nome do cargo/função" 
                :required="true" 
                class="col-md-6">
                
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fa {{ $isEdit ? 'fa-edit' : 'fa-save' }} mr-1"></i>
                        {{ $isEdit ? 'Atualizar' : 'Salvar' }}
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary ml-2">
                        <i class="fa fa-times mr-1"></i> Cancelar
                    </a>
                </div>
            </x-form-input>
        </div>
    </form>
</x-card>

@if($isEdit)
<x-card title="Usuários associados com o Cargo/Função" class="mt-4" :no-padding="true">
    <x-data-table 
        :headers="['ID', 'Nome', 'Username', 'Unidade', 'Data Criação', 'Status']" 
        empty-message="Nenhum usuário encontrado para este cargo/função">
        
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
    </x-data-table>
</x-card>
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