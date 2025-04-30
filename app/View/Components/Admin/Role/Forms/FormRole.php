<?php

namespace App\View\Components\Admin\Role\Forms;

use Illuminate\View\Component;

class FormRole extends Component
{
    public $editMode;
    public $role;

    public function __construct($editMode, $role = null)
    {
        $this->editMode = $editMode;
        $this->role = $role;
    }

    public function render()
    {
        return view("components.admin.role.forms.form-role");
    }
}