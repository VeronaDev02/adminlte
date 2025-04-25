<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Selfs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SelfsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $unidades = $user->unidades()->with('selfs')->get();
        
        $selfsList = collect();

        foreach ($unidades as $unidade) {
            $selfsList = $selfsList->merge($unidade->selfs()->active()->get());
        }
        
        $pdvDataList = [];

        foreach($selfsList as $self) {
            $rtspUrl = null;
            try {
                $encryptedUrl = $self->rtspUrl;
                if($encryptedUrl) {
                    $rtspUrl = Crypt::decryptString($encryptedUrl);
                }
            } catch(\Exception $e) {
                // Silenciosamente continua se houver erro
            }
            
            $pdvDataList[] = [
                'id' => $self->sel_id,
                'nome' => $self->sel_name,
                'pdvIp' => $self->sel_pdv_ip,
                'pdvCodigo' => $self->sel_pdv_codigo,
                'rtspUrl' => $rtspUrl,
            ];
        }

        // Obter parâmetros da requisição
        $activeQuadrants = $request->input('quadrants', null);
        $cols = $request->input('cols', null);
        $rows = $request->input('rows', null);
        
        // Verificar se PDVs específicos foram selecionados
        $selectedPdvs = $request->input('pdv', []);
        
        if (!empty($selectedPdvs) && $activeQuadrants) {
            // Reordenar a lista de PDVs para que os selecionados apareçam primeiro
            $orderedPdvList = [];
            
            foreach ($selectedPdvs as $pdvId) {
                $pdv = collect($pdvDataList)->firstWhere('id', $pdvId);
                if ($pdv) {
                    $orderedPdvList[] = $pdv;
                }
            }
            
            // Adicionar os demais PDVs não selecionados ao final da lista
            foreach ($pdvDataList as $pdv) {
                if (!in_array($pdv['id'], $selectedPdvs)) {
                    $orderedPdvList[] = $pdv;
                }
            }
            
            $pdvDataList = $orderedPdvList;
        }

        $serverConfig = [
            'rtspServer' => config('api_python.websocket_server'),
            'pdvServer' => config('api_python.websocket_pdv_server')
        ];
        
        // Inicializar o componente grid com os novos parâmetros
        $grid = new \App\View\Components\SelfsGrid(
            $pdvDataList, 
            $serverConfig,
            $activeQuadrants,
            $cols,
            $rows
        );
        
        return view('user.selfs.index', compact('pdvDataList', 'serverConfig', 'selfsList', 'grid'));
    }
}