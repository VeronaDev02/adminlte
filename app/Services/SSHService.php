<?php

namespace App\Services;

use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;
use Exception;
use Illuminate\Support\Facades\Log;

class SSHService
{
    public function createRemoteFile($host, $username, $password, $remoteFilePath, $content)
    {
        Log::info("Iniciando criação de arquivo remoto", [
            'host' => $host,
            'username' => $username,
            'file_path' => $remoteFilePath,
            'content_length' => strlen($content)
        ]);
        
        try {
            Log::debug("Inicializando cliente SFTP");
            $sftp = new SFTP($host);
            
            Log::debug("Tentando login SSH com usuário: $username");
            if (!$sftp->login($username, $password)) {
                throw new Exception('Falha no login: credenciais inválidas');
            }
            
            Log::info("Login SSH bem-sucedido");
            
            $directory = dirname($remoteFilePath);
            Log::debug("Verificando diretório: $directory");
            
            if (!$sftp->is_dir($directory)) {
                Log::debug("Diretório não existe, tentando criar: $directory");
                
                if (!$sftp->mkdir($directory, -1, true)) {
                    throw new Exception("Não foi possível criar o diretório: $directory");
                }
                
                Log::info("Diretório criado com sucesso: $directory");
            }
            
            Log::debug("Tentando criar arquivo: $remoteFilePath");
            if (!$sftp->put($remoteFilePath, $content)) {
                throw new Exception("Não foi possível criar o arquivo: $remoteFilePath");
            }
            
            Log::info("Arquivo criado com sucesso", [
                'path' => $remoteFilePath,
                'size' => strlen($content)
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Erro ao criar arquivo remoto', [
                'host' => $host,
                'username' => $username,
                'file_path' => $remoteFilePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    public function executeCommand($host, $username, $password, $command)
    {
        Log::info("Iniciando execução de comando SSH", [
            'host' => $host,
            'username' => $username,
            'command' => $command
        ]);
        
        try {
            Log::debug("Inicializando cliente SSH");
            $ssh = new SSH2($host);
            
            Log::debug("Tentando login SSH com usuário: $username");
            if (!$ssh->login($username, $password)) {
                throw new Exception('Falha no login: credenciais inválidas');
            }
            
            Log::info("Login SSH bem-sucedido");
            
            Log::debug("Executando comando: $command");
            $result = $ssh->exec($command);
            
            Log::info("Comando executado com sucesso", [
                'command' => $command,
                'result_length' => strlen($result)
            ]);
            
            $truncated_result = (strlen($result) > 500) ? substr($result, 0, 500) . '...' : $result;
            Log::debug("Resultado do comando:", ['result' => $truncated_result]);
            
            return $result;
        } catch (Exception $e) {
            Log::error('Erro ao executar comando remoto', [
                'host' => $host,
                'username' => $username,
                'command' => $command,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function toggleService($host, $username, $password, $serviceName, $enable = true)
    {
        $action = $enable ? "start" : "stop";
        
        Log::info("Alterando status do serviço", [
            'host' => $host,
            'username' => $username,
            'service' => $serviceName,
            'action' => $action
        ]);
        
        try {
            // Modificado para usar sudo -S e fornecer a senha via echo
            $command = "echo '{$password}' | sudo -S systemctl {$action} {$serviceName}";
            $result = $this->executeCommand($host, $username, $password, $command);
            
            $newStatus = $this->checkServiceStatus($host, $username, $password, $serviceName);
            $success = ($enable) ? $newStatus : !$newStatus;
            
            Log::info("Status do serviço alterado", [
                'service' => $serviceName,
                'action' => $action,
                'success' => $success,
                'result' => trim($result)
            ]);
            
            return $success;
        } catch (Exception $e) {
            Log::error('Erro ao alterar status do serviço', [
                'host' => $host,
                'username' => $username,
                'service' => $serviceName,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function checkServiceStatus($host, $username, $password, $serviceName)
    {
        Log::info("Verificando status do serviço", [
            'host' => $host,
            'username' => $username,
            'service' => $serviceName
        ]);
        
        try {
            // Verificar o status não requer sudo na maioria dos sistemas
            $command = "systemctl is-active {$serviceName}";
            $result = $this->executeCommand($host, $username, $password, $command);
            
            $isActive = trim($result) === "active";
            
            Log::info("Status do serviço verificado", [
                'service' => $serviceName,
                'is_active' => $isActive,
                'result' => trim($result)
            ]);
            
            return $isActive;
        } catch (Exception $e) {
            Log::error('Erro ao verificar status do serviço', [
                'host' => $host,
                'username' => $username,
                'service' => $serviceName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}