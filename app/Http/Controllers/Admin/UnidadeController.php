<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidade;
use App\Models\TipoUnidade;
use Illuminate\Http\Request;

class UnidadeController extends Controller
{
    public function index()
    {
        return view('admin.unidades.index');
    }

    public function create()
    {
        return view('admin.unidades.create');
    }

    public function edit($id)
    {
        return view('admin.unidades.edit', ['unidadeId' => $id]);
    }

    public function porTipo($codigo)
    {
        return view('admin.tipo-unidade.index', ['tipoCodigo' => $codigo]);
    }

    public function createPorTipo($codigo)
    {
        return view('admin.tipo-unidade.create', ['tipoCodigo' => $codigo]);
    }

    public function editPorTipo($codigo, $unidade)
    {
        return view('admin.tipo-unidade.edit', [
            'tipoCodigo' => $codigo,
            'unidadeId' => $unidade
        ]);
    }
    
    public function usuarios($id)
    {
        $unidade = Unidade::findOrFail($id);
        $usuarios = $unidade->users;
        
        return view('admin.unidades.usuarios', compact('unidade', 'usuarios'));
    }
}