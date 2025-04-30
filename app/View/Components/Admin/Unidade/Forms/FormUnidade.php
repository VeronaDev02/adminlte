<?php

namespace App\View\Components\Admin\Unidade\Forms;

use Illuminate\View\Component;

class FormUnidade extends Component
{
    public $editMode;
    public $unidade;
    public $tiposUnidade;
    public $usuarios;

    public function __construct($editMode, $tiposUnidade, $usuarios, $unidade = null)
    {
        $this->editMode = $editMode;
        $this->unidade = $unidade;
        $this->tiposUnidade = $tiposUnidade;
        $this->usuarios = $usuarios;
    }

    public function render()
    {
        return view("components.admin.unidade.forms.form-unidade");
    }
}