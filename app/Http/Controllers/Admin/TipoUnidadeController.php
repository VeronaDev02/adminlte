<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoUnidade;
use App\Models\Unidade;
use Illuminate\Http\Request;

class TipoUnidadeController extends Controller
{
    /**
     * Lista todas as unidades pertencentes a um tipo específico
     * 
     * @param int|string $codigo Código do tipo de unidade (tip_codigo)
     * @return \Illuminate\Http\Response
     */
    public function unidadesPorTipo($codigo)
    {
        // Verificamos se o tipo existe
        $tipoUnidade = TipoUnidade::where('tip_codigo', $codigo)->first();
        
        if (!$tipoUnidade) {
            session()->flash('error', 'Tipo de unidade não encontrado');
            return redirect()->route('unidades.index');
        }
        
        // Retorna a view com o código do tipo de unidade
        return view('admin.tipo-unidade.index', [
            'codigo' => $codigo,
            'tipoUnidade' => $tipoUnidade
        ]);
    }
    
    /**
     * Mostra o formulário para criar uma nova unidade para um tipo específico
     *
     * @param int|string $codigo Código do tipo de unidade (tip_codigo)
     * @return \Illuminate\Http\Response
     */
    public function createUnidade($codigo)
    {
        // Verificamos se o tipo existe
        $tipoUnidade = TipoUnidade::where('tip_codigo', $codigo)->first();
        
        if (!$tipoUnidade) {
            session()->flash('error', 'Tipo de unidade não encontrado');
            return redirect()->route('unidades.index');
        }
        
        // Retorna a view para criação com o código do tipo de unidade
        return view('admin.tipo-unidade.create', [
            'codigo' => $codigo,
            'tipoUnidade' => $tipoUnidade
        ]);
    }
    
    /**
     * Mostra o formulário para editar uma unidade de um tipo específico
     *
     * @param int|string $codigo Código do tipo de unidade (tip_codigo)
     * @param int $unidadeId ID da unidade
     * @return \Illuminate\Http\Response
     */
    public function editUnidade($codigo, $unidadeId)
    {
        // Verificamos se o tipo existe
        $tipoUnidade = TipoUnidade::where('tip_codigo', $codigo)->first();
        
        if (!$tipoUnidade) {
            session()->flash('error', 'Tipo de unidade não encontrado');
            return redirect()->route('unidades.index');
        }
        
        // Verificamos se a unidade existe e pertence ao tipo
        $unidade = Unidade::where('uni_id', $unidadeId)
            ->where('uni_tip_id', $tipoUnidade->tip_id)
            ->first();
            
        if (!$unidade) {
            session()->flash('error', 'Unidade não encontrada para este tipo');
            return redirect()->route('tipo-unidade.unidades', ['codigo' => $codigo]);
        }
        
        // Retorna a view para edição com o código do tipo e o ID da unidade
        return view('admin.tipo-unidade.edit', [
            'codigo' => $codigo,
            'unidadeId' => $unidadeId,
            'tipoUnidade' => $tipoUnidade,
            'unidade' => $unidade
        ]);
    }
}