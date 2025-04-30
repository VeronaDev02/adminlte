<div>
    <form class="form" id="form_role" method="POST"
        @if ($editMode) action="{{ route('admin.role.update', $role->rol_id) }}" @else action="{{ route('admin.role.store') }}" @endif>
        @csrf
        @if ($editMode)
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-3 form-group">
                <label for="rol_name">Nome da Função</label>
                <input type="text" class="form-control" id="rol_name" name="rol_name" placeholder="Nome da Função"
                    value="{{ old('rol_name', $editMode ? $role->rol_name : '') }}" required>
                @error('rol_name')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-2 form-group d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    @if ($editMode)
                        Atualizar
                    @else
                        Cadastrar
                    @endif
                </button>
            </div>
        </div>
    </form>
</div>

@push('css')
    <style>
        /* Remove o negrito do texto */
        #form_role label,
        #form_role input,
        #form_role button {
            font-weight: normal !important;
        }
    </style>
@endpush