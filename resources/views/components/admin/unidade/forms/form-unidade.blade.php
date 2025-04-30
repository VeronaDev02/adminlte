<div>
    @section('plugins.Bootstrapswitch', true)
    @section('plugins.BootstrapDualListbox', true)
    @section('plugins.Select2', true)
    @section('plugins.Inputmask', true)

    <form class="form" id="form_unidade" method="POST"
        @if ($editMode) action="{{ route('admin.unidade.update', $unidade->uni_id) }}" @else action="{{ route('admin.unidade.store') }}" @endif>
        @csrf
        @if ($editMode)
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-3 form-group">
                <label for="uni_codigo">Código da Unidade</label>
                <input type="text" class="form-control" id="uni_codigo" name="uni_codigo" placeholder="Código da Unidade"
                    value="{{ old('uni_codigo', $editMode ? $unidade->uni_codigo : '') }}">
                @error('uni_codigo')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-5 form-group">
                <label for="uni_nome">Nome da Unidade</label>
                <input type="text" class="form-control" id="uni_nome" name="uni_nome" placeholder="Nome da Unidade"
                    value="{{ old('uni_nome', $editMode ? $unidade->uni_nome : '') }}">
                @error('uni_nome')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-4 form-group">
                <label for="uni_tip_id">Tipo de Unidade</label>
                <select name="uni_tip_id" id="uni_tip_id" class="form-control">
                    <option value="">Selecione um tipo</option>
                    @forelse ($tiposUnidade as $tipo)
                        <option @if ($editMode && $unidade->uni_tip_id == $tipo->tip_id) selected @endif value="{{ $tipo->tip_id }}">
                            {{ $tipo->tip_nome }}
                        </option>
                    @empty
                        <option>Nenhum tipo de unidade encontrado</option>
                    @endforelse
                </select>
                @error('uni_tip_id')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="usuarios">Usuários</label>
            <select name="usuarios[]" id="usuarios" multiple>
                @foreach ($usuarios as $usuario)
                    <option 
                        @if ($editMode && in_array($usuario->use_id, $unidade->users->pluck('use_id')->toArray())) selected @endif 
                        value="{{ $usuario->use_id }}">
                        {{ $usuario->use_name }} ({{ $usuario->use_username }})
                    </option>
                @endforeach
            </select>
            @error('usuarios')
                <span class="invalid-feedback" style="display: unset" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="row mt-2">
            <div class="col-2 form-group">
                <button id="enviar" class="btn bg-primary">
                    @if ($editMode)
                        Atualizar
                    @else
                        Cadastrar
                    @endif
                </button>
            </div>
        </div>
    </form>

    @section('js')
        <script>
            $(document).ready(function() {
                $('#uni_tip_id').select2({
                    allowClear: false,
                    placeholder: "Selecione o Tipo de Unidade",
                });
                
                $('#usuarios').bootstrapDualListbox({
                    filterTextClear: 'Mostrar Todos',
                    filterPlaceHolder: 'Filtrar',
                    moveSelectedLabel: 'Mover Selecionados',
                    moveAllLabel: 'Mover Todos',
                    removeSelectedLabel: 'Remover Selecionados',
                    removeAllLabel: 'Remover Todos',
                    infoText: 'Mostrando Todos {0}',
                    infoTextFiltered: '<span class="badge badge-warning">Filtrando</span> {0} De {1}',
                    infoTextEmpty: 'Lista Vazia',
                });
            });
        </script>
    @endsection
    
    @push('css')
        <style>
            .select2-selection__rendered {
                line-height: 27px !important;
            }

            .select2-container .select2-selection--single {
                height: 38px !important;
            }

            .select2-selection__arrow {
                height: 34px !important;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                font-weight: normal !important;
            }

            .select2-container--default .select2-results__option[aria-selected=true] {
                font-weight: normal !important;
            }

            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                font-weight: normal !important;
            }
            /* Remove o negrito do texto */
            #form_unidade label,
            #form_unidade input,
            #form_unidade select,
            #form_unidade button,
            .bootstrap-duallistbox-container * {
                font-weight: normal !important;
            }
        </style>
    @endpush
</div>