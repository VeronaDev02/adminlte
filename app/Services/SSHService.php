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
            // Inicializa o cliente SFTP
            Log::debug("Inicializando cliente SFTP");
            $sftp = new SFTP($host);
            
            // Tenta conectar com o usuário e senha
            Log::debug("Tentando login SSH com usuário: $username");
            if (!$sftp->login($username, $password)) {
                throw new Exception('Falha no login: credenciais inválidas');
            }
            
            Log::info("Login SSH bem-sucedido");
            
            // Verifica se o diretório de destino existe
            $directory = dirname($remoteFilePath);
            Log::debug("Verificando diretório: $directory");
            
            if (!$sftp->is_dir($directory)) {
                Log::debug("Diretório não existe, tentando criar: $directory");
                
                // Tenta criar o diretório recursivamente
                if (!$sftp->mkdir($directory, -1, true)) {
                    throw new Exception("Não foi possível criar o diretório: $directory");
                }
                
                Log::info("Diretório criado com sucesso: $directory");
            }
            
            // Cria o arquivo no servidor remoto
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
            // Registra o erro e retorna falso
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
            // Inicializa o cliente SSH
            Log::debug("Inicializando cliente SSH");
            $ssh = new SSH2($host);
            
            // Tenta conectar com o usuário e senha
            Log::debug("Tentando login SSH com usuário: $username");
            if (!$ssh->login($username, $password)) {
                throw new Exception('Falha no login: credenciais inválidas');
            }
            
            Log::info("Login SSH bem-sucedido");
            
            // Executa o comando
            Log::debug("Executando comando: $command");
            $result = $ssh->exec($command);
            
            Log::info("Comando executado com sucesso", [
                'command' => $command,
                'result_length' => strlen($result)
            ]);
            
            // Log do resultado (truncado se for muito grande)
            $truncated_result = (strlen($result) > 500) ? substr($result, 0, 500) . '...' : $result;
            Log::debug("Resultado do comando:", ['result' => $truncated_result]);
            
            return $result;
        } catch (Exception $e) {
            // Registra o erro e retorna falso
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
}