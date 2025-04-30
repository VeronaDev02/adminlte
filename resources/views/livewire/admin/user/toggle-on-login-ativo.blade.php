<div>
    <button wire:click='updateStatusLoginAtivo' class="btn btn-xs btn-default text-primary mx-1 shadow" title="Ativar">
        @if ($user->use_login_ativo)
            <i class="fas fa-lg fa-fw fa-toggle-on" style="color: #00ff33;"></i>
        @else
            <i class="fas fa-lg fa-fw fa-toggle-off" style="color: #ff0000;"></i>
        @endif
    </button>
</div>