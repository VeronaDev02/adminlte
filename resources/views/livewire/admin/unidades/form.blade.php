@section('title', $title)
    
@section('content_header')
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Unidades', 'url' => route('unidades.index')],
        ['label' => $title, 'active' => true],
    ]" />
@stop

<div>
    <x-card :title="$title">
        <form wire:submit.prevent="save">
        <div class="row align-items-end">
                <div class="col-md-6">
                    <x-form-input 
                        label="Código" 
                        name="uni_codigo"
                        model-name="uni_codigo"
                        placeholder="Digite o código da unidade" 
                        :required="true" />
                </div>
                
                <div class="col-md-4">
                    <x-select2-input 
                        label="Tipo de Unidade" 
                        name="uni_tip_id"
                        :options="$tiposUnidade->pluck('tip_nome', 'tip_id')"
                        :selected-value="$uni_tip_id"
                        placeholder="Selecione um tipo"
                        :required="true" />
                </div>

                <div class="col-md-2">
                    <div class="form-group btn-align-wrapper">
                        <label class="invisible">Ações</label>
                        <div class="d-flex">
                            <button type="submit" 
                                    class="btn btn-primary btn-align-input mr-2" 
                                    wire:loading.attr="disabled" 
                                    wire:target="save">
                                {{ $isEdit ? 'Atualizar' : 'Salvar' }}
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin ml-2"></i>
                                </span>
                            </button>

                            <a href="{{ route('unidades.index') }}" class="btn btn-secondary btn-align-input">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <h4 class="mt-4">Gerenciamento de Usuários</h4>
            <div class="row">
                <div class="col-md-5">
                    <x-user-list 
                        title="Usuários Disponíveis"
                        card-class="card-primary"
                        :users="$usuarios"
                        :selected-ids="$usuariosSelecionados"
                        search-id="searchUsersDisponiveis"
                        table-id="usersDisponiveisTable"
                        action-type="add"
                        action-method="adicionarUsuario"
                        action-all-method="adicionarTodosUsuarios"
                        action-button-text="Adicionar Todos"
                        action-button-icon="fa-angle-double-right"
                        action-button-class="btn-primary" />
                </div>
                
                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <i class="fas fa-exchange-alt fa-2x text-muted mb-2"></i>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <x-user-list 
                        title="Usuários Selecionados"
                        card-class="card-success"
                        :users="$usuarios"
                        :selected-ids="$usuariosSelecionados"
                        search-id="searchUsersSelecionados"
                        table-id="usersSelecionadosTable"
                        action-type="remove"
                        action-method="removerUsuario"
                        action-all-method="removerTodosUsuarios"
                        action-button-text="Remover Todos"
                        action-button-icon="fa-angle-double-left"
                        action-button-class="btn-danger" />
                </div>
            </div>

            @if($isEdit)
            <x-card title="SelfCheckouts Associados" class="mt-4">
                <div class="row mb-3 justify-content-end">
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" 
                                wire:model.debounce.100ms="termoPesquisaSelfCheckouts"
                                class="form-control" 
                                placeholder="Pesquisar SelfCheckouts">
                        </div>
                    </div>
                </div>
                
                <x-data-table 
                    :headers="['Código', 'Nome', 'PDV Código', 'Status']"
                    empty-message="Nenhum SelfCheckout associado">
                    
                    @foreach($selfsAssociadosFiltrados as $self)
                    <tr>
                        <td wire:click="ordenarSelfCheckouts('sel_id')">{{ $self->sel_id }}</td>
                        <td wire:click="ordenarSelfCheckouts('sel_name')">{{ $self->sel_name }}</td>
                        <td wire:click="ordenarSelfCheckouts('sel_pdv_codigo')">{{ $self->sel_pdv_codigo }}</td>
                        <td>
                            @if($self->sel_status)
                                <span class="badge badge-success">Ativo</span>
                            @else
                                <span class="badge badge-danger">Inativo</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </x-data-table>
            </x-card>
            @endif
        </form>
    </x-card>
</div>

@section('css')
@stack('styles')
<style>
    td[wire\:click] {
        cursor: pointer;
    }
    td[wire\:click]:hover {
        background-color: #f1f1f1;
    }

    .btn-align-input {
        height: 38px;
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    .btn-align-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
</style>
@stop

@section('js')
@stack('scripts')
@stop