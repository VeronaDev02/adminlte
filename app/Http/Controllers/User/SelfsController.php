<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Selfs;

class SelfsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $selfsList = $user->unidades()
            ->with('selfs')
            ->get()
            ->flatMap(function($unidade) {
                return $unidade->selfs()->active()->get();
            });
        
        // Preparar a lista de PDVs
        $pdvDataList = $selfsList->map(function ($self) {
            return [
                'id' => $self->sel_id,
                'nome' => $self->sel_name,
                'pdvIp' => $self->sel_pdv_ip,
                'pdvCodigo' => $self->sel_pdv_codigo,
                'rtspUrl' => $self->sel_rtsp_path,
            ];
        })->toArray();

        $activeQuadrants = $request->input('quadrants', null);
        $cols = $request->input('cols', null);
        $rows = $request->input('rows', null);
        
        $selectedPdvs = $request->input('pdv', []);
        
        // Ordenar PDVs selecionados se houver seleção e quadrantes ativos
        if (!empty($selectedPdvs) && $activeQuadrants) {
            $pdvDataList = collect($pdvDataList)
                ->sortBy(function($pdv) use ($selectedPdvs) {
                    return !in_array($pdv['id'], $selectedPdvs);
                })
                ->values()
                ->all();
        }

        $unidade = !empty($selfsList) ? $selfsList->first()->unidade : null;

        $serverConfig = [
            'rtspServer' => $unidade ? $unidade->uni_api . ':8080' : config('api_python.websocket_server'),
            'pdvServer' => $unidade ? $unidade->uni_api . ':8765' : config('api_python.websocket_pdv_server')
        ];
        
        return view('user.selfs.index', compact('pdvDataList', 'serverConfig'));
    }
    
    public function show(Request $request)
    {
        $user = Auth::user();
        
        $selfsList = $user->unidades()
            ->with('selfs')
            ->get()
            ->flatMap(function($unidade) {
                return $unidade->selfs()->active()->get();
            });
        
        $pdvDataList = $selfsList->map(function ($self) {
            return [
                'id' => $self->sel_id,
                'nome' => $self->sel_name,
                'pdvIp' => $self->sel_pdv_ip,
                'pdvCodigo' => $self->sel_pdv_codigo,
                'rtspUrl' => $self->sel_rtsp_path,
            ];
        })->toArray();
        
        // Tratamento dos parâmetros de quadrantes - MOVIDO PARA ANTES DO USO
        $cols = $request->input('cols', 2);
        $rows = $request->input('rows', 2);
        $quadrants = $request->input('quadrants', 4);
        $selectedPdvs = $request->input('pdv', []);  // MOVIDO PARA ANTES DO USO
        
        // Obtenha a unidade do primeiro self, se existir
        $unidade = !empty($selfsList) ? $selfsList->first()->unidade : null;
        
        $serverConfig = [
            'rtspServer' => $unidade ? $unidade->uni_api . ':8080' : config('api_python.websocket_server'),
            'pdvServer' => $unidade ? $unidade->uni_api . ':8765' : config('api_python.websocket_pdv_server')
        ];
        
        // Prepare a configuração de conexão
        $connectionConfig = [
            'serverUrls' => [
                'rtsp' => $serverConfig['rtspServer'],
                'pdv' => $serverConfig['pdvServer'] 
            ],
            'connections' => []
        ];
        
        // Configure as conexões para cada PDV selecionado
        foreach($selectedPdvs as $index => $pdvId) {
            $pdv = collect($pdvDataList)->firstWhere('id', $pdvId);
            if ($pdv) {
                $connectionConfig['connections'][$index+1] = [
                    'pdvIp' => $pdv['pdvIp'],
                    'rtspUrl' => $pdv['rtspUrl'],
                    'selfId' => $pdv['id'],
                    'pdvCode' => $pdv['pdvCodigo']
                ];
            }
        }
        
        $pageTitle = 'Monitoramento de PDVs';
        
        // dd([
        //     'unidade' => $unidade ? $unidade->toArray() : null,
        //     'uni_api_raw' => $unidade ? $unidade->uni_api : null,
        //     'serverConfig' => $serverConfig,
        //     'connectionConfig' => $connectionConfig
        // ]);

        return view('user.selfs.monitor', compact(
            'pdvDataList', 
            'connectionConfig',
            'pageTitle', 
            'cols', 
            'rows', 
            'quadrants', 
            'selectedPdvs'
        ));
    }

    public function saveTelaPreferences(Request $request)
    {
        try {
            $user = auth()->user();
            
            $preferences = $request->input('preferences');
            
            $currentPreferences = $user->ui_preferences ?? [];
            
            if (!isset($currentPreferences['tela'])) {
                $currentPreferences['tela'] = [];
            }
            
            $currentPreferences['tela'][] = $preferences;
            
            $user->ui_preferences = $currentPreferences;
            $user->save();
            
            return response()->json(['success' => true, 'message' => 'Preferências de tela salvas com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyTelaPreferences(Request $request, $index)
    {
        try {
            $user = auth()->user();
            $currentPreferences = $user->ui_preferences ?? [];

            if (isset($currentPreferences['tela'][$index])) {
                unset($currentPreferences['tela'][$index]);
                $currentPreferences['tela'] = array_values($currentPreferences['tela']);

                $user->ui_preferences = $currentPreferences;
                $user->save();

                return response()->json(['success' => true, 'message' => 'Preferência removida com sucesso']);
            }

            return response()->json(['success' => false, 'message' => 'Preferência não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}