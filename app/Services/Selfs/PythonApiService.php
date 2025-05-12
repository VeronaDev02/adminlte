<?php

namespace App\Services\Selfs;

use App\Interfaces\Selfs\PythonApiServiceInterface;
use App\Models\Selfs;
use Illuminate\Support\Facades\Log;
use Exception;

class PythonApiService implements PythonApiServiceInterface
{
    protected $websocketPort;
    protected $pdvPort;
    protected $connection = null;
    protected $apiIp;

    public function __construct(Selfs $self = null)
    {
        // Portas padrão 
        $this->websocketPort = '8080';
        $this->pdvPort = '8765';
        
        // Se temos um self, obtemos o IP da API a partir da unidade
        if ($self && $self->unidade && $self->unidade->uni_api) {
            $this->apiIp = $self->unidade->uni_api;
        } else {
            // Caso contrário, usamos o valor do .env
            $this->apiIp = parse_url(config('api_python.websocket_server'), PHP_URL_HOST) ?: '127.0.0.1';
        }
        
        // Configuramos os servidores com o IP da unidade e as portas padrão
        $this->websocketServer = $this->apiIp . ':' . $this->websocketPort;
        $this->pdvServer = $this->apiIp . ':' . $this->pdvPort;
    }

    public function connect(string $pdvIp): bool
    {
        try {
            // Lógica de conexão WebSocket
            // Implementar conexão com servidor Python
            // Log::info("Conectando ao PDV: {$pdvIp}");
            return true;
        } catch (Exception $e) {
            // Log::error("Erro ao conectar PDV {$pdvIp}: " . $e->getMessage());
            return false;
        }
    }

    public function registerPDV(string $pdvIp): array
    {
        try {
            $registerCommand = [
                'command' => 'register',
                'pdv_ip' => $pdvIp
            ];

            return [
                'success' => true,
                'message' => "PDV {$pdvIp} registrado com sucesso"
            ];
        } catch (Exception $e) {
            // Log::error("Erro ao registrar PDV {$pdvIp}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function processMessage(string $message): array
    {
        try {
            $parsedMessage = json_decode($message, true);

            // Implementar processamento de mensagens
            switch ($parsedMessage['type'] ?? null) {
                case 'pdv_data':
                    return $this->processPDVData($parsedMessage);
                case 'pdv_inativo_timeout':
                    return $this->processInactivityAlert($parsedMessage);
                default:
                    Log::warning("Mensagem não reconhecida: " . $message);
                    return [];
            }
        } catch (Exception $e) {
            Log::error("Erro ao processar mensagem: " . $e->getMessage());
            return [];
        }
    }

    private function processPDVData(array $message): array
    {
        return [
            'pdv_ip' => $message['pdv_ip'] ?? null,
            'data' => $message['data'] ?? null,
            'timestamp' => now()
        ];
    }

    private function processInactivityAlert(array $message): array
    {
        return [
            'pdv_ip' => $message['pdv_ip'] ?? null,
            'inactive_time' => $message['inactive_time'] ?? 0,
            'alert_type' => 'inactivity'
        ];
    }

    public function checkPDVStatus(string $pdvIp): bool
    {
        try {
            // Implementar verificação de status do PDV
            return true;
        } catch (Exception $e) {
            Log::error("Erro ao verificar status do PDV {$pdvIp}: " . $e->getMessage());
            return false;
        }
    }
}