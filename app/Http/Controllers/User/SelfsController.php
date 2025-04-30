<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\Selfs\SelfsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SelfsController extends Controller
{
    protected $selfsRepository;

    public function __construct(SelfsRepository $selfsRepository)
    {
        $this->selfsRepository = $selfsRepository;
    }

    public function index(Request $request, SelfsRepository $repository)
    {
        $selfsList = $repository->getUserSelfs();
        
        $pdvDataList = $repository->preparePdvDataList($selfsList);

        $activeQuadrants = $request->input('quadrants', null);
        $cols = $request->input('cols', null);
        $rows = $request->input('rows', null);
        
        $selectedPdvs = $request->input('pdv', []);
        
        if (!empty($selectedPdvs) && $activeQuadrants) {
            $pdvDataList = $repository->orderSelectedPdvs($pdvDataList, $selectedPdvs);
        }

        $serverConfig = [
            'rtspServer' => config('api_python.websocket_server'),
            'pdvServer' => config('api_python.websocket_pdv_server')
        ];
        
        return view('user.selfs.index', compact('pdvDataList', 'serverConfig'));
    }

    public function saveTelaPreferences(Request $request)
    {
        try {
            $user = auth()->user();
            // \Log::info('Iniciando saveTelaPreferences', [
            //     'user_id' => $user->id,
            //     'request_data' => $request->all()
            // ]);
            
            $preferences = $request->input('preferences');
            // \Log::info('Preferências recebidas', ['preferences' => $preferences]);
            
            $currentPreferences = $user->ui_preferences ?? [];
            // \Log::info('Preferências atuais', ['currentPreferences' => $currentPreferences]);
            
            if (!isset($currentPreferences['tela'])) {
                $currentPreferences['tela'] = [];
                // \Log::info('Criando array tela vazio');
            }
            
            $currentPreferences['tela'][] = $preferences;
            // \Log::info('Preferências após adição', ['updatedPreferences' => $currentPreferences]);
            
            $user->ui_preferences = $currentPreferences;
            $result = $user->save();
            // \Log::info('Resultado do save()', ['result' => $result]);
            
            return response()->json(['success' => true, 'message' => 'Preferências de tela salvas com sucesso']);
        } catch (\Exception $e) {
            // \Log::error('Erro ao salvar preferências de tela', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);
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