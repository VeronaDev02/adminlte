<?php

namespace App\View\Components\Admin\Selfs\Forms;

use Illuminate\View\Component;
use App\Models\Unidade;
use App\Events\Admin\Selfs\Create;
use App\Events\Admin\Selfs\Edit;
use App\Models\Selfs;

class FormSelfs extends Component
{
    public $editMode;
    public $selfs;
    public $unidades;

    public function __construct($editMode, $unidades, $selfs = null)
    {
        $this->editMode = $editMode;
        $this->selfs = $selfs;
        $this->unidades = $unidades;
    }

    public function render()
    {
        return view("components.admin.selfs.forms.form-selfs");
    }
}