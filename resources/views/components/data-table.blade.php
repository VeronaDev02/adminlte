@if($responsive)
<div class="table-responsive">
@endif
    <table class="table 
        {{ $striped ? 'table-striped' : '' }} 
        {{ $bordered ? 'table-bordered' : '' }} 
        {{ $hover ? 'table-hover' : '' }} 
        {{ $attributes->get('class') }}">
        
        @if(count($headers) > 0)
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        @endif
        
        <tbody>
            {{ $slot }}
            
            @if(trim($slot) === '')
            <tr>
                <td colspan="{{ count($headers) }}" class="text-center">{{ $emptyMessage }}</td>
            </tr>
            @endif
        </tbody>
    </table>
@if($responsive)
</div>
@endif