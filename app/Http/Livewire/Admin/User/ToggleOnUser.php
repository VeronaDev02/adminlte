<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use App\Events\Admin\User\Edit;

class ToggleOnUser extends Component
{
    public $user;

    public function updateStatusUser()
    {
        try {
            $this->user->update([
                "use_active" => !$this->user->use_active,
            ]);
            
            event(new Edit($this->user->use_id, request()->ip()));
            
            if ($this->user->use_active) {
                $message = "Usuário " . $this->user->use_username . " foi ativado com sucesso";
            } else {
                $message = "Usuário " . $this->user->use_username . " foi desativado com sucesso";
            }
            
            $this->emit("updateStatusUser", ["status" => true, "message" => $message]);
        } catch (\Throwable $th) {
            $message = 'Erro ao alterar o status do usuário';
            $this->emit("updateStatusUser", ["status" => false, "message" => $message]);
        }
    }

    public function render()
    {
        return view("livewire.admin.user.toggle-on-user");
    }
}