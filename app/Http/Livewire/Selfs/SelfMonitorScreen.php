<?php

namespace App\Http\Livewire\Selfs;

use Livewire\Component;
use App\Repositories\Selfs\SelfsRepository;
use App\Services\Selfs\WebSocketService;
use App\Services\Selfs\PythonApiService;
use Illuminate\Support\Facades\Log;

class SelfMonitorScreen extends Component
{
    public $quadrants = 0;
    public $columns = 0;
    public $rows = 0;
    public $selectedPdvs = [];
    public $pdvData = [];
    
    // Configurações do servidor
    public $rtspServerUrl;
    public $pdvServerUrl;
    
    // Serviços injetados
    protected $selfsRepository;
    protected $webSocketService;
    protected $pythonApiService;
    
    public function boot(
        SelfsRepository $selfsRepository,
        WebSocketService $webSocketService,
        PythonApiService $pythonApiService
    ) {
        $this->selfsRepository = $selfsRepository;
        $this->webSocketService = $webSocketService;
        $this->pythonApiService = $pythonApiService;
    }
    
    public function mount(SelfsRepository $selfsRepository)
    {
        // Obter parâmetros da URL
        $this->quadrants = request()->input('quadrants', 0);
        $this->columns = request()->input('cols', 0);
        $this->rows = request()->input('rows', 0);
        $this->selectedPdvs = request()->input('pdv', []);
        
        // Configurações do servidor - Importante: remover o http:// e a porta
        $this->rtspServerUrl = config('api_python.websocket_server', '127.0.0.1:8080');
        $this->pdvServerUrl = config('api_python.websocket_pdv_server', '127.0.0.1:8765');
        
        // Carregar dados dos PDVs
        if (!empty($this->selectedPdvs)) {
            $selfsList = $selfsRepository->getUserSelfs();
            $allPdvData = $selfsRepository->preparePdvDataList($selfsList);
            
            // Filtrar apenas os PDVs selecionados
            foreach ($this->selectedPdvs as $position => $pdvId) {
                $pdv = $selfsRepository->findPdvById($allPdvData, $pdvId);
                if ($pdv) {
                    $this->pdvData[$position] = $pdv;
                }
            }
        }
        
        // Log::info('SelfMonitorScreen montado', [
        //     'quadrants' => $this->quadrants,
        //     'columns' => $this->columns,
        //     'rows' => $this->rows,
        //     'selectedPdvs' => $this->selectedPdvs,
        //     'pdvCount' => count($this->pdvData)
        // ]);

        // foreach ($this->pdvData as $position => $pdv) {
        //     Log::info("PDV na posição {$position}", [
        //         'id' => $pdv['id'],
        //         'nome' => $pdv['nome'],
        //         'pdvIp' => $pdv['pdvIp'],
        //         'rtspUrl' => $pdv['rtspUrl']
        //     ]);
        // }
    }
    
    protected function loadPdvData()
    {
        if (empty($this->selectedPdvs)) {
            return;
        }
        
        $selfsList = $this->selfsRepository->getUserSelfs();
        $allPdvData = $this->selfsRepository->preparePdvDataList($selfsList);
        
        // Filtrar apenas os PDVs selecionados
        foreach ($this->selectedPdvs as $position => $pdvId) {
            $pdv = $this->selfsRepository->findPdvById($allPdvData, $pdvId);
            if ($pdv) {
                $this->pdvData[$position] = $pdv;
            }
        }
    }
    
    public function getConnectionConfigData()
    {
        // Preparar configuração para o JavaScript
        $connectionConfig = [
            'serverUrls' => [
                'rtsp' => $this->rtspServerUrl,
                'pdv' => $this->pdvServerUrl
            ],
            'connections' => []
        ];
        
        foreach ($this->pdvData as $position => $pdv) {
            $connectionConfig['connections'][$position] = [
                'selfId' => $pdv['id'],
                'pdvIp' => $pdv['pdvIp'],
                'rtspUrl' => $pdv['rtspUrl'],
                'pdvCode' => $pdv['pdvCodigo'],
                'name' => $pdv['nome']
            ];
        }
        
        return $connectionConfig;
    }
    
    public function render()
    {
        $connectionConfig = $this->getConnectionConfigData();
        
        return view('livewire.selfs.self-monitor-screen', [
            'pdvData' => $this->pdvData,
            'connectionConfig' => $connectionConfig
        ]);
    }
}