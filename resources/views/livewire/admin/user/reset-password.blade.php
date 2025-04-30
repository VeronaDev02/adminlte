<div class="d-flex justify-content-center">
    <button class="btn bg-primary" data-toggle="modal" data-target="#modal{{ $user->use_id }}"> Redefinir senha </button>
    <div wire:ignore.self class="modal fade" id="modal{{ $user->use_id }}" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Resetar senha</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if (!$newPassword)
                    <div class="modal-body d-flex flex-column align-items-center">
                        <h5>
                            Você tem certeza que deseja resetar a senha do usuário
                        </h5>
                        <br>
                        <h4 class="d-flex flex-column align-items-center">
                            {{ $user->use_name }}
                            @if ($user->role)
                                ({{ $user->role->rol_nome }})
                            @endif
                        </h4>
                    </div>
                    <div class="modal-body d-flex flex-column align-items-center">
                        <button style="width:100%" type="button" class="btn btn-primary"
                            wire:click='resetPassword'>Resetar</button>
                        <button style="width:100%" type="button" class="btn btn-secondary m-1"
                            data-dismiss="modal">Cancelar</button>
                    </div>
                @else
                    <div class="modal-body d-flex flex-column align-items-center">
                        <br>
                        <h4>
                            Usuário: {{ $user->use_username }}
                        </h4>
                        <h4>
                            Nova senha: {{ $newPassword }}
                        </h4>
                    </div>
                    <div class="modal-body d-flex flex-column align-items-center">
                        {{-- <button style="width:100%" type="button" class="btn btn-primary"
                            wire:click='resetPassword'>Outra senha</button> --}}
                        <button style="width:100%" type="button" class="btn btn-secondary m-1"
                            data-dismiss="modal">Voltar</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>