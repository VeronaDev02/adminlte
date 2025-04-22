<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select2Input extends Component
{
    public $label;
    public $name;
    public $options;
    public $selectedValue;
    public $placeholder;
    public $required;
    
    public function __construct(
        $label, 
        $name, 
        $options = [], 
        $selectedValue = null, 
        $placeholder = 'Selecione uma opção', 
        $required = false
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->options = $options;
        $this->selectedValue = $selectedValue;
        $this->placeholder = $placeholder;
        $this->required = $required;
    }

    public function render()
    {
        return view('components.select2-input');
    }
}