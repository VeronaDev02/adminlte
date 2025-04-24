<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $id;
    public $title;
    public $type;
    public $size;
    public $showFooter;

    public function __construct(
        $id,
        $title = 'Modal',
        $type = 'default',
        $size = 'md',
        $showFooter = true
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->size = $size;
        $this->showFooter = $showFooter;
    }

    public function render()
    {
        return view('components.modal');
    }
}