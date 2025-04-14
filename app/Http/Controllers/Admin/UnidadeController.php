<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidade;
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

    public function show($id)
    {
        $unidade = Unidade::findOrFail($id);
        return view('admin.unidades.show', compact('unidade'));
    }

    public function search(Request $request)
    {
        $query = Unidade::query();

        if ($request->has('codigo') && !empty($request->codigo)) {
            $query->where('uni_codigo', 'like', '%' . $request->codigo . '%');
        }

        if ($request->has('descricao') && !empty($request->descricao)) {
            $query->where('uni_descricao', 'like', '%' . $request->descricao . '%');
        }

        if ($request->has('cidade') && !empty($request->cidade)) {
            $query->where('uni_cidade', 'like', '%' . $request->cidade . '%');
        }

        if ($request->has('uf') && !empty($request->uf)) {
            $query->where('uni_uf', $request->uf);
        }

        $unidades = $query->get();

        return view('admin.unidades.index', compact('unidades'));
    }

    public function usuarios($id)
    {
        $unidade = Unidade::findOrFail($id);
        $usuarios = $unidade->todosUsuarios;
        
        return view('admin.unidades.usuarios', compact('unidade', 'usuarios'));
    }

    public function processarUsuarios(Request $request, $id)
    {
        return redirect()->route('unidades.edit', $id)
            ->with('error', 'Processamento de usuários agora é feito pelo componente Livewire');
    }
}