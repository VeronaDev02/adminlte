<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    public $title;
    public $hasTools = false;
    public $noPadding = false;

    public function __construct($title = null, $hasTools = false, $noPadding = false)
    {
        $this->title = $title;
        $this->hasTools = $hasTools;
        $this->noPadding = $noPadding;
    }

    public function render()
    {
        return view('components.card');
    }
}