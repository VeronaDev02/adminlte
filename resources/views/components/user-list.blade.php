<div class="card card-outline {{ $cardClass }}">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
        <div class="card-tools">
            <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" 
                    id="{{ $searchId }}" 
                    data-table="{{ $tableId }}"
                    class="form-control float-right search-input" 
                    placeholder="Pesquisar">
                <div class="input-group-append">
                    <button type="button" class="btn btn-default">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
        <table class="table table-striped table-bordered">
            <tbody id="{{ $tableId }}">
                @if($actionType === 'add')
                    @foreach($users as $user)
                        @if(!in_array($user->use_id, $selectedIds))
                        <tr data-user-id="{{ $user->use_id }}">
                            <td>{{ $user->use_name }}</td>
                            <td class="text-right" style="width: 80px;">
                                <button type="button" 
                                        wire:click="{{ $actionMethod }}({{ $user->use_id }})" 
                                        class="btn btn-xs btn-default text-primary mx-1 shadow">
                                    <i class="fa fa-lg fa-fw fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @else
                    @foreach($users as $user)
                        @if(in_array($user->use_id, $selectedIds))
                        <tr data-user-id="{{ $user->use_id }}">
                            <td>{{ $user->use_name }}</td>
                            <td class="text-right" style="width: 80px;">
                                <button type="button" 
                                        wire:click="{{ $actionMethod }}({{ $user->use_id }})" 
                                        class="btn btn-xs btn-default text-danger mx-1 shadow">
                                    <i class="fa fa-lg fa-fw fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="card-footer text-center">
        <button type="button" 
                class="btn btn-sm {{ $actionButtonClass }}" 
                wire:click="{{ $actionAllMethod }}"
                data-toggle="tooltip"
                title="{{ $actionButtonText }}">
            <i class="fas {{ $actionButtonIcon }}"></i> {{ $actionButtonText }}
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInputs = document.querySelectorAll('.search-input');
        
        searchInputs.forEach(input => {
            input.addEventListener('keyup', function() {
                const tableId = this.getAttribute('data-table');
                const filter = this.value.toUpperCase();
                const table = document.getElementById(tableId);
                const rows = table.getElementsByTagName('tr');
                
                Array.from(rows).forEach(row => {
                    const td = row.querySelector('td');
                    if (td) {
                        const txtValue = td.textContent || td.innerText;
                        row.style.display = txtValue.toUpperCase().includes(filter) ? '' : 'none';
                    }
                });
            });
        });
    });
</script>
@endpush