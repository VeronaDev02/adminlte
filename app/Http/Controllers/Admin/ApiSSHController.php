<?php

namespace App\Http\Controllers\Admin;

use App\Models\Selfs;
use App\Http\Controllers\Controller;
use App\Services\SshService;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiSSHController extends Controller
{
    protected $sshService;
    
    public function __construct(SshService $sshService)
    {
        $this->sshService = $sshService;
    }

    public function createConfig(Request $request, $unidadeId)
    {
        Log::info("Iniciando criação de configuração SSH", ['unidade_id' => $unidadeId]);
        
        try {
            $unidade = Unidade::findOrFail($unidadeId);
            Log::debug("Unidade encontrada", [
                'unidade_id' => $unidade->uni_id,
                'unidade_nome' => $unidade->uni_nome,
                'api' => $unidade->uni_api
            ]);
            
            if (empty($unidade->uni_api) || empty($unidade->uni_api_login) || empty($unidade->uni_api_password)) {
                Log::warning("Dados de configuração SSH ausentes", [
                    'unidade_id' => $unidadeId,
                    'has_api' => !empty($unidade->uni_api),
                    'has_api_login' => !empty($unidade->uni_api_login),
                    'has_api_password' => !empty($unidade->uni_api_password)
                ]);
                
                return redirect()->back()
                    ->with('error', 'Faltam dados de configuração SSH para esta unidade.');
            }
            
            $selfs = Selfs::where('sel_uni_id', $unidadeId)
                        ->where('sel_status', true)
                        ->get();
            
            Log::info("SelfCheckouts encontrados", ['count' => count($selfs)]);
            
            $selfsData = [];
            foreach ($selfs as $self) {
                $selfsData[] = [
                    'pdv_ip' => $self->sel_pdv_ip,
                    'pdv_port' => $self->sel_pdv_listen_port,
                    'dvr_ip' => $self->sel_dvr_ip,
                    'dvr_port' => $self->sel_dvr_port,
                    'rtsp_url' => $self->sel_rtsp_path
                ];
                
                Log::debug("Self adicionado à configuração", [
                    'self_id' => $self->sel_id,
                    'pdv_ip' => $self->sel_pdv_ip,
                    'pdv_port' => $self->sel_pdv_listen_port
                ]);
            }
            
            $fileContent = json_encode($selfsData, JSON_PRETTY_PRINT);
            Log::debug("Conteúdo JSON gerado", ['length' => strlen($fileContent)]);
            
            $host = $unidade->uni_api;
            $username = $unidade->uni_api_login;
            $password = $unidade->uni_api_password;
            
            $filePath = "/home/{$unidade->uni_api_login}/api/config.json";
            Log::info("Caminho do arquivo remoto", ['path' => $filePath]);
            
            Log::debug("Valores usados para conexão SSH:", [
                'host_original' => $unidade->uni_api,
                'host_final' => $host,
                'username_original' => $unidade->uni_api_login,
                'username_final' => $username,
                'password_length' => $password ? strlen($password) : 0
            ]);

            // Cria o arquivo no servidor remoto
            $result = $this->sshService->createRemoteFile(
                $host, $username, $password, $filePath, $fileContent
            );
            
            if ($result) {
                Log::info("Arquivo de configuração criado com sucesso", [
                    'unidade_id' => $unidadeId,
                    'selfs_count' => count($selfsData)
                ]);
                
                return redirect()->back()
                    ->with('success', 'Arquivo de configuração criado com sucesso! Selfs ativos incluídos: ' . count($selfsData));
            }
            
            Log::error("Falha ao criar arquivo de configuração", [
                'unidade_id' => $unidadeId,
                'host' => $host
            ]);
            
            return redirect()->back()
                ->with('error', 'Falha ao criar arquivo de configuração. Verifique os logs para mais detalhes.');
                
        } catch (\Exception $e) {
            Log::error("Exceção ao criar configuração SSH", [
                'unidade_id' => $unidadeId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erro ao processar a configuração: ' . $e->getMessage());
        }
    }

     public function checkApiStatus($unidadeId)
    {
        Log::info("Verificando status da API", ['unidade_id' => $unidadeId]);
        
        try {
            $unidade = Unidade::findOrFail($unidadeId);
            
            if (empty($unidade->uni_api) || empty($unidade->uni_api_login) || empty($unidade->uni_api_password)) {
                Log::warning("Dados de configuração SSH ausentes", [
                    'unidade_id' => $unidadeId
                ]);
                
                return false;
            }
            
            $serviceName = 'api';
            
            $status = $this->sshService->checkServiceStatus(
                $unidade->uni_api,
                $unidade->uni_api_login,
                $unidade->uni_api_password,
                $serviceName
            );
            
            return $status;
        } catch (\Exception $e) {
            Log::error("Erro ao verificar status da API", [
                'unidade_id' => $unidadeId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    public function toggleApiStatus(Request $request, $unidadeId)
    {
        $enable = $request->input('enable', true);
        
        Log::info("Alterando status da API", [
            'unidade_id' => $unidadeId,
            'enable' => $enable
        ]);
        
        try {
            $unidade = Unidade::findOrFail($unidadeId);
            
            if (empty($unidade->uni_api) || empty($unidade->uni_api_login) || empty($unidade->uni_api_password)) {
                Log::warning("Dados de configuração SSH ausentes", [
                    'unidade_id' => $unidadeId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Faltam dados de configuração SSH para esta unidade'
                ]);
            }
            
            $serviceName = 'api';
            
            $success = $this->sshService->toggleService(
                $unidade->uni_api,
                $unidade->uni_api_login,
                $unidade->uni_api_password,
                $serviceName,
                $enable
            );
            
            if ($success) {
                $message = $enable ? 
                    'API ativada com sucesso para a unidade ' . $unidade->uni_nome :
                    'API desativada com sucesso para a unidade ' . $unidade->uni_nome;
                    
                return response()->json([
                    'success' => true,
                    'status' => $enable,
                    'message' => $message
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Falha ao alterar o status da API'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erro ao alterar status da API", [
                'unidade_id' => $unidadeId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ]);
        }
    }
}