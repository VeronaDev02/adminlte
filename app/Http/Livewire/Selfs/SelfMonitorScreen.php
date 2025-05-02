<?php

namespace App\Http\Livewire\Selfs;

use Livewire\Component;
use App\Models\Selfs;
use App\Services\Selfs\GridLayoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SelfMonitorScreen extends Component
{
    public $quadrants = 0;
    public $columns = 0;
    public $rows = 0;
    public $selectedPdvs = [];
    public $pdvData = [];
    public $pageTitle = "Monitoramento de PDVs";
    
    // Configurações do servidor
    public $rtspServerUrl;
    public $pdvServerUrl;
    
    protected $gridLayoutService;
    
    public function boot(GridLayoutService $gridLayoutService)
    {
        $this->gridLayoutService = $gridLayoutService;
    }
    
    public function mount()
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
        $this->loadPdvData();
        
        // Gerar o título personalizado da página
        $this->generatePageTitle();
        
        Log::info('SelfMonitorScreen montado', [
            'quadrants' => $this->quadrants,
            'columns' => $this->columns,
            'rows' => $this->rows,
            'selectedPdvs' => $this->selectedPdvs,
            'pdvCount' => count($this->pdvData)
        ]);
    }
    
    protected function loadPdvData()
    {
        if (empty($this->selectedPdvs)) {
            return;
        }
        
        $user = Auth::user();
        
        // Obter todos os selfs do usuário através de suas unidades
        $selfsList = $user->unidades()
            ->with('selfs')
            ->get()
            ->flatMap(function($unidade) {
                return $unidade->selfs()->active()->get();
            });
        
        // Preparar a lista de PDVs
        $allPdvData = $selfsList->map(function ($self) {
            return [
                'id' => $self->sel_id,
                'nome' => $self->sel_name,
                'pdvIp' => $self->sel_pdv_ip,
                'pdvCodigo' => $self->sel_pdv_codigo,
                'rtspUrl' => $self->sel_rtsp_path,
            ];
        })->toArray();
        
        // Filtrar apenas os PDVs selecionados
        foreach ($this->selectedPdvs as $position => $pdvId) {
            $pdv = $this->findPdvById($allPdvData, $pdvId);
            if ($pdv) {
                $this->pdvData[$position] = $pdv;
            }
        }
    }
    
    protected function findPdvById($pdvDataList, $pdvId)
    {
        foreach ($pdvDataList as $pdv) {
            if ($pdv['id'] == $pdvId) {
                return $pdv;
            }
        }
        return null;
    }
    
    protected function generatePageTitle()
    {
        // Título base
        $this->pageTitle = "SelfCheckout ||";
        
        $user = Auth::user();
        $configName = "";
        $pdvNames = [];
        
        // Busca nome da configuração se estiver salva
        if ($user->ui_preferences && isset($user->ui_preferences['tela'])) {
            foreach ($user->ui_preferences['tela'] as $tela) {
                if ($tela['quadrants'] == $this->quadrants && 
                    $tela['columns'] == $this->columns && 
                    $tela['rows'] == $this->rows &&
                    $this->areSelectedPdvsEqual($tela['selectedPdvs'], $this->selectedPdvs)) {
                    
                    $configName = $tela['display_name'] ?? 'Monitor ||';
                    break;
                }
            }
        }
        
        // Busca nomes de todos os PDVs selecionados
        if (!empty($this->pdvData)) {
            foreach ($this->selectedPdvs as $position => $pdvId) {
                $pdv = $this->pdvData[$position] ?? null;
                if ($pdv && !empty($pdv['pdvCodigo'])) {
                    // Pega os dois últimos dígitos do código do PDV
                    $pdvCode = substr($pdv['pdvCodigo'], -2);
                    $pdvNames[] = $pdvCode;
                }
            }
        }
        
        // Monta o título completo de forma mais concisa
        $titleParts = [];
        
        if (!empty($configName)) {
            $titleParts[] = $configName;
        }
        
        if (!empty($pdvNames)) {
            // Limita a 4 PDVs para evitar título muito longo
            $limitedPdvNames = array_slice($pdvNames, 0, 4);
            if (count($pdvNames) > 4) {
                $limitedPdvNames[] = '+ ' . (count($pdvNames) - 4);
            }
            $titleParts[] = implode(' ', $limitedPdvNames);
        }
        
        // Combina partes do título
        if (!empty($titleParts)) {
            $this->pageTitle .= " " . implode(' ', $titleParts);
        }
    }
    
    protected function areSelectedPdvsEqual($savedPdvs, $requestPdvs)
    {
        if (count($savedPdvs) != count($requestPdvs)) {
            return false;
        }
        
        foreach ($savedPdvs as $key => $value) {
            if (!isset($requestPdvs[$key]) || $requestPdvs[$key] != $value) {
                return false;
            }
        }
        
        return true;
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
            'connectionConfig' => $connectionConfig,
            'pageTitle' => $this->pageTitle
        ]);
    }
}