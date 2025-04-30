<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use App\Events\Admin\User\Edit;

class ToggleOnLoginAtivo extends Component
{
    public $user;

    public function updateStatusLoginAtivo()
    {
        try {
            $this->user->update([
                "use_login_ativo" => !$this->user->use_login_ativo,
            ]);
            
            event(new Edit($this->user->use_id, request()->ip()));
            
            if ($this->user->use_login_ativo) {
                $message = "Login do usuário " . $this->user->use_username . " foi ativado com sucesso";
            } else {
                $message = "Login do usuário " . $this->user->use_username . " foi desativado com sucesso";
            }
            
            $this->emit("updateStatusLoginAtivo", ["status" => true, "message" => $message]);
        } catch (\Throwable $th) {
            $message = 'Erro ao alterar o status do login do usuário';
            $this->emit("updateStatusLoginAtivo", ["status" => false, "message" => $message]);
        }
    }

    public function render()
    {
        return view("livewire.admin.user.toggle-on-login-ativo");
    }
}