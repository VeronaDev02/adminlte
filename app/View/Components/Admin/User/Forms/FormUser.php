<?php

namespace App\View\Components\Admin\User\Forms;

use Illuminate\View\Component;

class FormUser extends Component
{
    public $editMode;
    public $user;
    public $roles;
    public $unidades;

    public function __construct($editMode, $unidades, $roles, $user = null)
    {
        $this->editMode = $editMode;
        $this->user = $user;
        $this->roles = $roles;
        $this->unidades = $unidades;
    }

    public function render()
    {
        return view("components.admin.user.forms.form-user");
    }
}