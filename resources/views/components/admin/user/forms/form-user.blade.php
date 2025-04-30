<div>
    @section('plugins.Bootstrapswitch', true)
    @section('plugins.BootstrapDualListbox', true)
    @section('plugins.Select2', true)
    @section('plugins.Inputmask', true)

    <form class="form" id="form_user" method="POST"
        @if ($editMode) action="{{ route('admin.user.update', $user->use_id) }}" @else action="{{ route('admin.user.store') }}" @endif>
        @csrf
        @if ($editMode)
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-1 form-group">
                <label for="use_active">Ativo</label><br>
                <input class="form-control" id="use_active" name="use_active" type="checkbox" value="1"
                    @if (!$editMode || ($editMode && $user->use_active)) checked @endif>
                @error('use_active')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-2 form-group">
                <label for="use_cod_func">Código do Funcionário</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="use_cod_func" id="use_cod_func"
                        placeholder="Código" aria-label="Código do Funcionário" aria-describedby="basic-addon2"
                        value="{{ old('use_cod_func', $editMode && isset($user->use_cod_func) ? $user->use_cod_func : '') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-info" id="btnGetFuncionario" type="button">
                            <i id="originalIcon" class="fas fa-check"></i>
                            <i id="loadingIcon" class="fas fa-spinner fa-spin" style="display: none;"></i>
                        </button>
                    </div>
                    @error('use_cod_func')
                        <span class="invalid-feedback" style="display: unset;" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-2 form-group">
                <label for="use_name">Nome</label>
                <input type="text" onkeyup="gera_username()" onchange="gera_username()" class="form-control"
                    id="use_name" name="use_name" placeholder="Nome"
                    value="{{ old('use_name', $editMode ? $user->use_name : '') }}">
                @error('use_name')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-2 form-group">
                <label for="use_username">Usuário</label>
                <input class="form-control" readonly id="use_username" name="use_username" type="text"
                    value="{{ old('use_username', $editMode ? $user->use_username : '') }}">
                @error('use_username')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-md-2 col-6 form-group">
                <label for="use_cpf">CPF</label>
                <input class="form-control" id="use_cpf" name="use_cpf" placeholder="CPF" type="text"
                    value="{{ old('use_cpf', $editMode && isset($user->use_cpf) ? $user->use_cpf : '') }}">
                @error('use_cpf')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-3 form-group">
                <label for="use_email">Email (Opcional)</label>
                <input class="form-control" id="use_email" name="use_email" type="email"
                    placeholder="email@example.com"
                    value="{{ old('use_email', $editMode ? $user->use_email : '') }}">
                @error('use_email')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-2 form-group">
                <label for="use_cell">Telefone (Opcional)</label>
                <input class="form-control" id="use_cell" name="use_cell" type="text" placeholder="(00) 00000-0000"
                    value="{{ old('use_cell', $editMode ? $user->use_cell : '') }}">
                @error('use_cell')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-2 form-group">
                <label for="use_rol_id">Função</label>
                <select name="use_rol_id" id="use_rol_id" class="form-control">
                    <option value="">Selecione uma função</option>
                    @forelse ($roles as $role)
                        <option @if ($editMode && $user->use_rol_id == $role->rol_id) selected @endif value="{{ $role->rol_id }}">
                            {{ $role->rol_name }}
                        </option>
                    @empty
                        <option>Nenhuma função encontrada</option>
                    @endforelse
                </select>
                @error('use_rol_id')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-1 form-group">
                <label for="use_login_ativo">Login Ativo</label><br>
                <input class="form-control" id="use_login_ativo" name="use_login_ativo" type="checkbox" value="1"
                    @if (!$editMode || ($editMode && $user->use_login_ativo)) checked @endif>
                @error('use_login_ativo')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-1 form-group">
                <label for="use_allow_updates">Atualizações</label><br>
                <input class="form-control" id="use_allow_updates" name="use_allow_updates" type="checkbox" value="1"
                    @if ($editMode && $user->use_allow_updates) checked @endif>
                @error('use_allow_updates')
                    <span class="invalid-feedback" style="display: unset;" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="unidades">Unidades</label>
            <select name="unidades[]" id="unidades" multiple>
                @foreach ($unidades as $unidade)
                    <option 
                        @if ($editMode && in_array($unidade->uni_id, $user->unidades->pluck('uni_id')->toArray())) selected @endif 
                        value="{{ $unidade->uni_id }}">
                        {{ $unidade->uni_codigo }} - {{ $unidade->uni_nome }}
                    </option>
                @endforeach
            </select>
            @error('unidades')
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
            function gera_username() {
                let nome = document.getElementById('use_name').value.trim().split(" ");
                if (nome.length < 2) {
                    document.getElementById('use_username').value = "";
                    return;
                }
                
                let primeiroNome = nome[0].normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase();
                let sobrenome = nome[nome.length - 1].normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase();
                
                primeiroNome = primeiroNome.replace(/[^a-zA-Z0-9]/g, "");
                sobrenome = sobrenome.replace(/[^a-zA-Z0-9]/g, "");
                
                document.getElementById('use_username').value = primeiroNome + '_' + sobrenome;
                if (primeiroNome === sobrenome) {
                    document.getElementById('use_username').value = "";
                }
            }

            $(document).ready(function() {
                $('input[type="checkbox"]').bootstrapSwitch({
                    onText: 'SIM',
                    offText: 'NÃO'
                });

                $('#use_cod_func').keypress(function(e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        $('#btnGetFuncionario').click();
                    }
                });

                $('#btnGetFuncionario').click(function() {
                    $('#originalIcon').hide();
                    $('#loadingIcon').show();

                    var codFuncionario = $('#use_cod_func').val();
                    var token = '{{ csrf_token() }}';

                    $.ajax({
                        url: "{{ route('admin.user.getFuncionario') }}",
                        method: 'POST',
                        data: {
                            use_cod_func: codFuncionario,
                            _token: token,
                        },
                        success: function(response) {
                            $('#use_name').val(response.nome);
                            $('#use_cpf').val(response.cpf)
                            gera_username();
                            $('#originalIcon').show();
                            $('#loadingIcon').hide();
                        },
                        error: function(response) {
                            console.log(response)
                            toastr.error('Nenhum Funcionário encontrado.');
                            $('#originalIcon').show();
                            $('#loadingIcon').hide();
                            $('#use_name').val('');
                            $('#use_cpf').val('');
                        }
                    });
                });
            });

            $(function() {
                $('input[type="checkbox"]').bootstrapSwitch();
                $('#use_cell').inputmask("(99) 99999-9999");
                $('#use_rol_id').select2({
                    allowClear: false,
                    placeholder: "Selecione a Função",
                });
                
                $('#unidades').bootstrapDualListbox({
                    filterTextClear: 'Mostrar Todas',
                    filterPlaceHolder: 'Filtrar',
                    moveSelectedLabel: 'Mover Selecionadas',
                    moveAllLabel: 'Mover Todas',
                    removeSelectedLabel: 'Remover Selecionadas',
                    removeAllLabel: 'Remover Todas',
                    infoText: 'Mostrando Todas {0}',
                    infoTextFiltered: '<span class="badge badge-warning">Filtrando</span> {0} De {1}',
                    infoTextEmpty: 'Lista Vazia',
                });
                
                $('#use_cpf').inputmask('999.999.999-99', {
                    "placeholder": "___.___.___-__"
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
            
            /* Remove o negrito do texto */
            #form_user label,
            #form_user input,
            #form_user select,
            #form_user button,
            .bootstrap-duallistbox-container * {
                font-weight: normal !important;
            }
        </style>
    @endpush
</div>