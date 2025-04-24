<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-{{ $size }}" role="document">
        <div class="modal-content">
            <div class="modal-header {{ $type !== 'default' ? 'bg-'.$type : '' }}">
                <h5 class="modal-title {{ $type !== 'default' ? 'text-white' : '' }}" id="{{ $id }}Label">
                    @if($type === 'danger')
                        <i class="fas fa-exclamation-triangle"></i>
                    @endif
                    {{ $title }}
                </h5>
                <button type="button" class="close {{ $type !== 'default' ? 'text-white' : '' }}" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if($showFooter)
            <div class="modal-footer">
                {{ $footer ?? '' }}
            </div>
            @endif
        </div>
    </div>
</div>