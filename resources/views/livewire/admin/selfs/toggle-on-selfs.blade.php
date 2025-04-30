<div>
    <button wire:click='updateStatusSelfs' class="btn btn-xs btn-default text-primary mx-1 shadow" title="Ativar">
        @if ($selfs->sel_status)
            <i class="fas fa-lg fa-fw fa-toggle-on" style="color: #00ff33;"></i>
        @else
            <i class="fas fa-lg fa-fw fa-toggle-off" style="color: #ff0000;"></i>
        @endif
    </button>
</div>