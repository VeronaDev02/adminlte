@section('plugins.Sweetalert2', true)
<div>
    @foreach($menuItems as $item)
        @if(isset($item['submenu']))
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon {{ $item['icon'] }}"></i>
                    <p>
                        {{ $item['text'] }}
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @foreach($item['submenu'] as $subItem)
                        <li class="nav-item">
                            <a href="{{ $subItem['url'] }}" class="nav-link" target="_blank">
                                <i class="nav-icon {{ $subItem['icon'] }}"></i>
                                <p>
                                    {{ $subItem['text'] }}
                                    <button 
                                        class="btn btn-danger btn-sm delete-tela"
                                        onclick="event.preventDefault(); event.stopPropagation(); showSweetAlert({{ $subItem['index'] }});"
                                    >
                                        Excluir
                                    </button>
                                </p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @else
            <li class="nav-item">
                <a href="{{ $item['url'] }}" class="nav-link"  target="_blank">
                    <i class="nav-icon {{ $item['icon'] }}"></i>
                    <p>{{ $item['text'] }}</p>
                </a>
            </li>
        @endif
    @endforeach

    <script>
        function showSweetAlert(index) {
            Swal.fire({
                title: 'Atenção!',
                text: "Não há como reverter esta ação!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('executeDelete', index);
                }
            });
        }
    </script>
</div>