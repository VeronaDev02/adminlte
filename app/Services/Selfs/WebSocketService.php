<?php

namespace App\Services\Selfs;

use App\Interfaces\Selfs\PythonApiServiceInterface;
use Illuminate\Support\Facades\Log;
use Ratchet\Client\WebSocket;
use Exception;

class WebSocketService
{
    protected $pythonApiService;
    protected $connections = [];

    public function __construct(PythonApiServiceInterface $pythonApiService)
    {
        $this->pythonApiService = $pythonApiService;
    }

    public function connectToPDV(string $pdvIp)
    {
        try {
            if (isset($this->connections[$pdvIp])) {
                return $this->connections[$pdvIp];
            }

            $connection = $this->pythonApiService->connect($pdvIp);
            
            if ($connection) {
                $this->connections[$pdvIp] = $connection;
                $registrationResult = $this->pythonApiService->registerPDV($pdvIp);
                
                return $registrationResult;
            }

            return false;
        } catch (Exception $e) {
            Log::error("Erro ao conectar WebSocket para PDV {$pdvIp}: " . $e->getMessage());
            return false;
        }
    }

    public function handleIncomingMessage(string $message)
    {
        try {
            $processedMessage = $this->pythonApiService->processMessage($message);
            
            return $processedMessage;
        } catch (Exception $e) {
            Log::error("Erro ao processar mensagem WebSocket: " . $e->getMessage());
            return null;
        }
    }

    public function closeConnection(string $pdvIp)
    {
        if (isset($this->connections[$pdvIp])) {
            // Implementar lógica de fechamento de conexão
            unset($this->connections[$pdvIp]);
        }
    }
}