<div class="row mb-2">
    <div class="col-sm-6"></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            @foreach($items as $item)
                @if(isset($item['active']) && $item['active'])
                    <li class="breadcrumb-item active">{{ $item['label'] }}</li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</div>