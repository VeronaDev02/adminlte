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
        
        // Obter todos os selfs do usuário através de suas unidades
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

        $serverConfig = [
            'rtspServer' => config('api_python.websocket_server'),
            'pdvServer' => config('api_python.websocket_pdv_server')
        ];
        
        return view('user.selfs.index', compact('pdvDataList', 'serverConfig'));
    }
    
    public function show(Request $request)
    {
        $user = Auth::user();
        
        // Obter todos os selfs do usuário através de suas unidades
        $selfsList = $user->unidades()
            ->with('selfs')
            ->get()
            ->flatMap(function($unidade) {
                return $unidade->selfs()->active()->get();
            });
        
        // Preparar a lista de PDVs para a view de monitor
        $pdvDataList = $selfsList->map(function ($self) {
            return [
                'id' => $self->sel_id,
                'nome' => $self->sel_name,
                'pdvIp' => $self->sel_pdv_ip,
                'pdvCodigo' => $self->sel_pdv_codigo,
                'rtspUrl' => $self->sel_rtsp_path,
            ];
        })->toArray();
        
        $serverConfig = [
            'rtspServer' => config('api_python.websocket_server'),
            'pdvServer' => config('api_python.websocket_pdv_server')
        ];
        
        return view('user.selfs.monitor', compact('pdvDataList', 'serverConfig'));
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