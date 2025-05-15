<?php

namespace App\Observers;

use App\Models\Selfs;
use App\Http\Controllers\Admin\ApiSSHController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SelfsObserver
{
    /**
     * Handle the Selfs "created" event.
     *
     * @param  \App\Models\Selfs  $selfs
     * @return void
     */
    public function created(Selfs $selfs)
    {
        $this->updateConfigFile($selfs->sel_uni_id);
    }

    /**
     * Handle the Selfs "updated" event.
     *
     * @param  \App\Models\Selfs  $selfs
     * @return void
     */
    public function updated(Selfs $selfs)
    {
        $this->updateConfigFile($selfs->sel_uni_id);
    }

    /**
     * Handle the Selfs "deleted" event.
     *
     * @param  \App\Models\Selfs  $selfs
     * @return void
     */
    public function deleted(Selfs $selfs)
    {
        $this->updateConfigFile($selfs->sel_uni_id);
    }

    /**
     * Atualiza o arquivo de configuração
     */
    private function updateConfigFile($unidadeId)
    {
        try {
            // Obtém uma instância do controller via container do Laravel
            $controller = App::make(ApiSSHController::class);
            
            // Cria uma request vazia
            $request = new Request();
            
            // Chama o método existente para criar/atualizar o arquivo de configuração
            $controller->createConfig($request, $unidadeId);
            
            \Log::info("Arquivo de configuração atualizado para a unidade ID: $unidadeId");
        } catch (\Exception $e) {
            \Log::error("Erro ao atualizar arquivo de configuração: " . $e->getMessage());
        }
    }
}