<div class="card {{ $attributes->get('class') }}">
    @if($title)
    <div class="card-header">
        <div class="row">
            <h3 class="col-md-10">{{ $title }}</h3>
            @if($hasTools)
                <div class="col-md-2">
                    {{ $tools ?? '' }}
                </div>
            @endif
        </div>
    </div>
    @endif
    <div class="card-body {{ $noPadding ? 'p-0' : '' }}">
        {{ $slot }}
    </div>
</div>