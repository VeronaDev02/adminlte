<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoUnidade;
use App\Models\Unidade;
use Illuminate\Http\Request;

class TipoUnidadeController extends Controller
{
    public function unidadesPorTipo($codigo)
    {
        $tipoUnidade = TipoUnidade::where('tip_codigo', $codigo)->first();
        
        if (!$tipoUnidade) {
            session()->flash('error', 'Tipo de unidade não encontrado');
            return redirect()->route('unidades.index');
        }
        
        return view('admin.tipo-unidade.index', [
            'codigo' => $codigo,
            'tipoUnidade' => $tipoUnidade
        ]);
    }
    
    public function createUnidade($codigo)
    {
        $tipoUnidade = TipoUnidade::where('tip_codigo', $codigo)->first();
        
        if (!$tipoUnidade) {
            session()->flash('error', 'Tipo de unidade não encontrado');
            return redirect()->route('unidades.index');
        }
        
        return view('admin.tipo-unidade.create', [
            'codigo' => $codigo,
            'tipoUnidade' => $tipoUnidade
        ]);
    }
    
    public function editUnidade($codigo, $unidadeId)
    {
        $tipoUnidade = TipoUnidade::where('tip_codigo', $codigo)->first();
        
        if (!$tipoUnidade) {
            session()->flash('error', 'Tipo de unidade não encontrado');
            return redirect()->route('unidades.index');
        }
        
        $unidade = Unidade::where('uni_id', $unidadeId)
            ->where('uni_tip_id', $tipoUnidade->tip_id)
            ->first();
            
        if (!$unidade) {
            session()->flash('error', 'Unidade não encontrada para este tipo');
            return redirect()->route('tipo-unidade.unidades', ['codigo' => $codigo]);
        }
        
        return view('admin.tipo-unidade.edit', [
            'codigo' => $codigo,
            'unidadeId' => $unidadeId,
            'tipoUnidade' => $tipoUnidade,
            'unidade' => $unidade
        ]);
    }
}