<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DataTable extends Component
{
    public $headers;
    public $striped;
    public $bordered;
    public $hover;
    public $responsive;
    public $emptyMessage;

    public function __construct(
        $headers = [],
        $striped = true,
        $bordered = true,
        $hover = true,
        $responsive = true,
        $emptyMessage = 'Nenhum registro encontrado'
    ) {
        $this->headers = $headers;
        $this->striped = $striped;
        $this->bordered = $bordered;
        $this->hover = $hover;
        $this->responsive = $responsive;
        $this->emptyMessage = $emptyMessage;
    }

    public function render()
    {
        return view('components.data-table');
    }
}