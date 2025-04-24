<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormInput extends Component
{
    public $label;
    public $name;
    public $placeholder;
    public $required;
    public $type;
    public $value;
    public $modelName;

    /**
     * Create a new component instance.
     *
     * @param string $label
     * @param string $name
     * @param string $placeholder
     * @param bool $required
     * @param string $type
     * @param mixed $value
     * @param string $modelName
     * @return void
     */
    public function __construct(
        $label, 
        $name, 
        $placeholder = null, 
        $required = false, 
        $type = 'text', 
        $value = null,
        $modelName = null
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->placeholder = $placeholder ?? 'Digite o ' . strtolower($label);
        $this->required = $required;
        $this->type = $type;
        $this->value = $value;
        $this->modelName = $modelName ?? $name;
    }

    public function render()
    {
        return view('components.form-input');
    }
}