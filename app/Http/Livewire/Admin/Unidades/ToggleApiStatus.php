<?php

namespace App\Http\Livewire\Admin\Unidades;

use Livewire\Component;
use App\Models\Unidade;
use App\Http\Controllers\Admin\ApiSSHController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ToggleApiStatus extends Component
{
    public $unidade;
    public $apiStatus = false;
    public $isLoading = false;
    public $lastChecked = null;
    public $errorMessage = null;

    public function mount()
    {
        $this->checkStatus();
    }
    
    public function checkStatus()
    {
        $this->isLoading = true;
        $this->errorMessage = null;
        
        try {
            $controller = App::make(ApiSSHController::class);
            $this->apiStatus = $controller->checkApiStatus($this->unidade->uni_id);
            $this->lastChecked = now();
            
            Log::info("Status da API verificado via Livewire", [
                'unidade_id' => $this->unidade->uni_id,
                'status' => $this->apiStatus
            ]);
        } catch (\Exception $e) {
            $this->errorMessage = "Erro ao verificar status: " . $e->getMessage();
            
            Log::error("Erro ao verificar status da API via Livewire", [
                'unidade_id' => $this->unidade->uni_id,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Erro ao verificar status da API: ' . $e->getMessage()
            ]);
        }
        
        $this->isLoading = false;
    }

    public function toggleApiStatus()
    {
        $this->isLoading = true;
        $this->errorMessage = null;
        
        try {
            $controller = App::make(ApiSSHController::class);
            
            // Cria uma request com o novo status desejado
            $request = new \Illuminate\Http\Request();
            $request->merge(['enable' => !$this->apiStatus]);
            
            Log::info("Tentando alterar status da API via Livewire", [
                'unidade_id' => $this->unidade->uni_id,
                'novo_status' => !$this->apiStatus
            ]);
            
            // Chama o método para alterar o status
            $response = $controller->toggleApiStatus($request, $this->unidade->uni_id);
            $data = json_decode($response->getContent(), true);
            
            if ($data['success']) {
                $this->apiStatus = $data['status'];
                $this->lastChecked = now();
                
                Log::info("Status da API alterado com sucesso via Livewire", [
                    'unidade_id' => $this->unidade->uni_id,
                    'novo_status' => $this->apiStatus
                ]);
                
                $this->dispatchBrowserEvent('notify', [
                    'type' => 'success',
                    'message' => $data['message']
                ]);
            } else {
                $this->errorMessage = $data['message'];
                
                Log::warning("Falha ao alterar status da API via Livewire", [
                    'unidade_id' => $this->unidade->uni_id,
                    'mensagem' => $data['message']
                ]);
                
                $this->dispatchBrowserEvent('notify', [
                    'type' => 'error',
                    'message' => $data['message']
                ]);
            }
        } catch (\Exception $e) {
            $this->errorMessage = "Erro ao alterar status: " . $e->getMessage();
            
            Log::error("Exceção ao alterar status da API via Livewire", [
                'unidade_id' => $this->unidade->uni_id,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Erro ao alterar status da API: ' . $e->getMessage()
            ]);
        }
        
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.admin.unidades.toggle-api-status');
    }
}