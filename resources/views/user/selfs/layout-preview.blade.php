<div class="layout-preview-container">

    <div class="layout-grid" style="
        display: grid;
        grid-template-columns: repeat({{ $columns }}, 1fr);
        grid-template-rows: repeat({{ $rows }}, 1fr);
        gap: 5px;
        background-color: #343a40;
        padding: 10px;
        border-radius: 5px;
        height: 200px;
    ">
        @for($i = 0; $i < $quadrants; $i++)
            <div class="layout-cell" style="
                background-color: #5a636a;
                border-radius: 3px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
            ">
                {{ $i + 1 }}
            </div>
        @endfor
    </div>
</div>