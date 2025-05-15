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
            // Busca a unidade no banco de dados
            $unidade = Unidade::findOrFail($unidadeId);
            Log::debug("Unidade encontrada", [
                'unidade_id' => $unidade->uni_id,
                'unidade_nome' => $unidade->uni_nome,
                'api' => $unidade->uni_api
            ]);
            
            // Verifica se a unidade tem as informações necessárias
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
            
            // Busca os SelfCheckouts ativos desta unidade
            $selfs = Selfs::where('sel_uni_id', $unidadeId)
                        ->where('sel_status', true)
                        ->get();
            
            Log::info("SelfCheckouts encontrados", ['count' => count($selfs)]);
            
            // Prepara os dados para o arquivo de configuração
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
            
            // Cria o conteúdo do arquivo JSON
            $fileContent = json_encode($selfsData, JSON_PRETTY_PRINT);
            Log::debug("Conteúdo JSON gerado", ['length' => strlen($fileContent)]);
            
            // Define as credenciais SSH
            $host = $unidade->uni_api;
            $username = $unidade->uni_api_login;
            $password = $unidade->uni_api_password;
            
            // Define o caminho do arquivo
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
}