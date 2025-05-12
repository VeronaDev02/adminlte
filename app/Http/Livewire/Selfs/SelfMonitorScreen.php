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
    
    // Novo estado para gerenciar logs e status
    public $pdvStatus = [];
    public $pdvLogs = [];
    // public $serverStatus = "Conectando ao servidor...";
    public $serverStatusClass = "";
    public $activeFullscreenQuadrant = null;
    public $isBrowserFullscreen = false;
    
    // Configurações do servidor
    public $rtspServerUrl;
    public $pdvServerUrl;
    
    protected $gridLayoutService;
    
    // Defina listeners para eventos do JavaScript
    protected $listeners = [
        'updateStatus' => 'updateStatus',
        // 'serverConnectionStatusChanged' => 'updateServerStatus',
        'pdvConnectionAttempt' => 'handlePdvConnectionAttempt',
        'pdvConnectionError' => 'handlePdvConnectionError',
        'handleRegisterResponse' => 'handleRegisterResponse',
        'handlePdvData' => 'handlePdvData',
        'handleInactivityAlert' => 'handleInactivityAlert',
        'quadrantFullscreenChanged' => 'updateFullscreenQuadrant',
        'browserFullscreenChanged' => 'updateBrowserFullscreen',
    ];
    
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
        
        // Carregar dados dos PDVs
        $this->loadPdvData();
        
        // Buscar diretamente o primeiro PDV selecionado e sua unidade
        if (!empty($this->selectedPdvs)) {
            $firstPdvId = reset($this->selectedPdvs); // Pega o primeiro PDV selecionado independente da posição
            $self = Selfs::with('unidade')->find($firstPdvId);
            
            if ($self && $self->unidade && $self->unidade->uni_api) {
                // Use o IP da unidade com as portas padrão
                $apiIp = $self->unidade->uni_api;
                $this->rtspServerUrl = $apiIp . ':8080';
                $this->pdvServerUrl = $apiIp . ':8765';
            } else {
                // Fallback para as configurações do .env, use 127.0.0.1 como último recurso
                $this->rtspServerUrl = env('WEBSOCKET_SERVER_PYTHON', '127.0.0.1:8080');
                $this->pdvServerUrl = env('WEBSOCKET_PDV_SERVER_PYTHON', '127.0.0.1:8765');
            }
        } else {
            // Se não houver PDVs selecionados, use 127.0.0.1 como último recurso
            $this->rtspServerUrl = env('WEBSOCKET_SERVER_PYTHON', '127.0.0.1:8080');
            $this->pdvServerUrl = env('WEBSOCKET_PDV_SERVER_PYTHON', '127.0.0.1:8765');
        }
        
        // Inicializar arrays de status e logs
        foreach ($this->pdvData as $position => $pdv) {
            $this->pdvStatus[$position] = [
                'message' => 'Desconectado',
                'class' => ''
            ];
            $this->pdvLogs[$position] = [];
        }
        
        // Gerar o título personalizado da página
        $this->generatePageTitle();
        
        Log::info('SelfMonitorScreen montado', [
            'quadrants' => $this->quadrants,
            'columns' => $this->columns,
            'rows' => $this->rows,
            'selectedPdvs' => $this->selectedPdvs,
            'pdvCount' => count($this->pdvData),
            'rtspServerUrl' => $this->rtspServerUrl,  // Log para debug
            'pdvServerUrl' => $this->pdvServerUrl     // Log para debug
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
        $this->pageTitle = "SelfCheckout ||";
        
        $user = Auth::user();
        $configName = "";
        $pdvNames = [];
        
        if ($user->ui_preferences && isset($user->ui_preferences['tela'])) {
            foreach ($user->ui_preferences['tela'] as $tela) {
                if ($tela['quadrants'] == $this->quadrants && 
                    $tela['columns'] == $this->columns && 
                    $tela['rows'] == $this->rows &&
                    $this->areSelectedPdvsEqual($tela['selectedPdvs'], $this->selectedPdvs)) {
                    
                    $configName = 'Monitor || ';
                    break;
                }
            }
        }
        
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
        
    public function updateStatus($position, $message, $class = '')
    {
        $this->pdvStatus[$position] = [
            'message' => $message,
            'class' => $class
        ];
    }
    
    // public function updateServerStatus($connected, $statusClass = '')
    // {
    //     $this->serverStatus = $connected ? 'Conectado ao servidor' : 'Desconectado do servidor';
    //     $this->serverStatusClass = $connected ? 'connected' : $statusClass;
    // }
    
    // public function handlePdvConnectionAttempt($position, $pdvIp)
    // {
    //     $timestamp = now()->format('H:i:s');
    //     $this->pdvLogs[$position][] = "[{$timestamp}] [INFO] Conectando ao PDV {$pdvIp}...";
    //     $this->updateStatus($position, 'Conectando PDV...');
    // }
    
    // public function handlePdvConnectionError($position, $pdvIp, $errorMessage)
    // {
    //     $timestamp = now()->format('H:i:s');
    //     $this->pdvLogs[$position][] = "[{$timestamp}] [ERRO] Falha na conexão com o PDV: {$errorMessage}";
    //     $this->updateStatus($position, 'Erro PDV', 'error');
    // }
    
    // public function handleRegisterResponse($pdvIp, $success)
    // {
    //     // Obter posição pelo IP do PDV
    //     $position = $this->getPdvPositionByIp($pdvIp);
        
    //     if ($position) {
    //         $timestamp = now()->format('H:i:s');
            
    //         if ($success) {
    //             $this->pdvLogs[$position][] = "[{$timestamp}] [INFO] Registrado no PDV {$pdvIp}";
    //             $this->updateStatus($position, "Conectado - PDV {$pdvIp}", 'connected');
    //         } else {
    //             $this->pdvLogs[$position][] = "[{$timestamp}] [ERRO] Falha ao registrar no PDV {$pdvIp}";
    //             $this->updateStatus($position, 'Falha - PDV', 'error');
    //         }
    //     }
    // }
    
    public function handlePdvData($pdvIp, $message)
    {
        // Obter posição pelo IP do PDV
        $position = $this->getPdvPositionByIp($pdvIp);
        
        if ($position) {
            // $timestamp = now()->format('H:i:s');
            
            // Escapar caracteres HTML na mensagem
            $safeMessage = htmlspecialchars($message);

            $safeMessage = trim($safeMessage);
            // $safeMessage = preg_replace('/\n+/', ' |', $safeMessage);
            $safeMessage = preg_replace('/\s+/', ' ', $safeMessage);


            // $this->pdvLogs[$position][] = "[{$timestamp}] {$safeMessage}";
            $this->pdvLogs[$position][] = "{$safeMessage}";

            if (count($this->pdvLogs[$position]) > 100) {
                array_shift($this->pdvLogs[$position]);
            }

            $this->emit('logsUpdated');
        }
    }
    
    public function handleInactivityAlert($pdvIp, $inactiveTime)
    {
        // Obter posição pelo IP do PDV
        $position = $this->getPdvPositionByIp($pdvIp);
        
        if ($position) {
            // Adiciona mensagem ao log (opcional, descomentado para melhor visibilidade)
            $timestamp = now()->format('H:i:s');
            $this->pdvLogs[$position][] = "[{$timestamp}] [ALERTA] PDV inativo por {$inactiveTime} segundos!";
            
            // Define um atributo para marcar o quadrante com alerta
            $this->dispatchBrowserEvent('inactivity-alert', [
                'position' => $position,
                'pdvIp' => $pdvIp,
                'inactiveTime' => $inactiveTime,
                'timestamp' => $timestamp
            ]);
        }
    }
    
    public function updateFullscreenQuadrant($position)
    {
        $this->activeFullscreenQuadrant = $position;
    }
    
    public function updateBrowserFullscreen($isFullscreen)
    {
        $this->isBrowserFullscreen = $isFullscreen;
    }
    
    protected function getPdvPositionByIp($pdvIp)
    {
        foreach ($this->pdvData as $position => $pdv) {
            if ($pdv['pdvIp'] === $pdvIp) {
                return $position;
            }
        }
        
        return null;
    }
    
    public function render()
    {
        $connectionConfig = $this->getConnectionConfigData();
        
        return view('livewire.selfs.self-monitor-screen', [
            'pdvData' => $this->pdvData,
            'connectionConfig' => $connectionConfig,
            'pageTitle' => $this->pageTitle,
            'pdvStatus' => $this->pdvStatus,
            'pdvLogs' => $this->pdvLogs,
            // 'serverStatus' => $this->serverStatus,
            'serverStatusClass' => $this->serverStatusClass,
            'activeFullscreenQuadrant' => $this->activeFullscreenQuadrant,
            'isBrowserFullscreen' => $this->isBrowserFullscreen
        ]);
    }
}