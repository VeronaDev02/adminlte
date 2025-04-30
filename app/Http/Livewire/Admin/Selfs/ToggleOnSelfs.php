<?php

namespace App\Http\Livewire\Admin\Selfs;

use Livewire\Component;
use App\Events\Admin\Selfs\Edit;

class ToggleOnSelfs extends Component
{
    public $selfs;

    public function updateStatusSelfs()
    {
        try {
            $this->selfs->update([
                "sel_status" => !$this->selfs->sel_status,
            ]);
            
            event(new Edit($this->selfs->sel_id, request()->ip()));
            
            if ($this->selfs->sel_status) {
                $message = "SelfCheckout " . $this->selfs->sel_name . " foi ativado com sucesso";
            } else {
                $message = "SelfCheckout " . $this->selfs->sel_name . " foi desativado com sucesso";
            }
            
            $this->emit("updateStatusSelfs", ["status" => true, "message" => $message]);
        } catch (\Throwable $th) {
            $message = 'Erro ao alterar o status do SelfCheckout';
            $this->emit("updateStatusSelfs", ["status" => false, "message" => $message]);
        }
    }

    public function render()
    {
        return view("livewire.admin.selfs.toggle-on-selfs");
    }
}