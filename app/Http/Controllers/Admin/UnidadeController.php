<?php

namespace App\Http\Controllers\Admin;

use App\Events\Admin\Unidade\Create as UnidadeCreateEvent;
use App\Events\Admin\Unidade\Edit as UnidadeEditEvent;
use App\Events\Admin\Unidade\Delete as UnidadeDeleteEvent;
use App\Events\Admin\Unit\Create as UnitCreateEvent;
use App\Events\Admin\Unit\Delete as UnitDeleteEvent;
use App\Http\Controllers\Controller;
use App\Models\Unidade;
use App\Models\Unit;
use App\Models\User;
use App\Models\Selfs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UnidadeController extends Controller
{
    public function index()
    {
        $unidades = Unidade::orderBy('uni_id', 'asc')->paginate(15);
        return view('admin.unidades.index', compact('unidades'));
    }

    public function create()
    {
        // Buscar todos os usuários disponíveis
        $todosUsuarios = User::orderBy('use_name')->get();
        
        return view('admin.unidades.create', compact('todosUsuarios'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uni_codigo' => 'required|regex:"^\\d+$" |max:3|unique:unidade,uni_codigo',
            'uni_descricao' => 'required|string|max:255',
            'uni_cidade' => 'required|string|max:100',
            'uni_uf' => 'required|string|size:2',
        ], [
            'uni_codigo.unique' => 'O código da unidade já está sendo utilizado.',
            'uni_codigo.required' => 'O código da unidade é obrigatório.',
            'uni_codigo.max' => 'O código da unidade deve ter no máximo 3 dígitos.',
            'uni_codigo.regex' => 'O código da unidade deve conter apenas números.',
            'uni_descricao.required' => 'O nome da unidade é obrigatório.',
            'uni_cidade.required' => 'A cidade é obrigatória.',
            'uni_uf.required' => 'A UF é obrigatória.',
            'uni_uf.size' => 'A UF deve ter 2 caracteres.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()]);
            }
            
            return redirect()->route('unidades.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Criar a unidade
            $unidade = Unidade::create($request->only([
                'uni_codigo', 'uni_descricao', 'uni_cidade', 'uni_uf'
            ]));
            
            event(new UnidadeCreateEvent($unidade->uni_id, request()->ip()));

            // Processar usuários selecionados
            if ($request->filled('usuarios_vincular')) {
                $usuariosParaVincular = json_decode($request->input('usuarios_vincular'), true);
                
                foreach ($usuariosParaVincular as $userId) {
                    // Criar o vínculo na tabela units
                    $unit = Unit::create([
                        'unit_uni_id' => $unidade->uni_id,
                        'unit_use_id' => $userId
                    ]);
                    event(new UnitCreateEvent($unit->unit_id, request()->ip()));
                }
            }
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('unidades.index'),
                    'message' => 'Unidade criada com sucesso!'
                ]);
            }
            
            return redirect()->route('unidades.index')
                ->with('success', 'Unidade criada com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar unidade: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->route('unidades.create')
                ->with('error', 'Erro ao criar unidade: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $unidade = Unidade::findOrFail($id);
        return view('unidades.show', compact('unidade'));
    }

    public function edit($id)
    {
        $unidade = Unidade::findOrFail($id);
        
        // Busca todos os selfs que não estão associados a unidade
        $selfsDisponiveis = Selfs::whereNotIn('sel_id', function($query) use ($id) {
            $query->select('sel_id')
                ->from('selfs')
                ->where('sel_uni_id', $id);
        })->get();
        
        // Busca todos os usuários que não estão associados a unidade pela tabela units
        $usuariosAssociados = Unit::where('unit_uni_id', $id)->pluck('unit_use_id')->toArray();
        $usuariosDisponiveis = User::whereNotIn('use_id', $usuariosAssociados)->get();
        
        return view('admin.unidades.edit', compact('unidade', 'selfsDisponiveis', 'usuariosDisponiveis'));
    }

    public function update(Request $request, $id)
    {
        $unidade = Unidade::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'uni_codigo' => 'required|regex:"^\\d+$" |max:3|unique:unidade,uni_codigo,' . $id . ',uni_id',
            'uni_descricao' => 'required|string|max:255',
            'uni_cidade' => 'required|string|max:100',
            'uni_uf' => 'required|string|size:2',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()]);
            }
            
            return redirect()->route('unidades.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $unidade->update($request->only(['uni_codigo', 'uni_descricao', 'uni_cidade', 'uni_uf']));
        event(new UnidadeEditEvent($unidade->uni_id, request()->ip()));

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'redirect' => route('unidades.index'),
                'message' => 'Unidade atualizada com sucesso!'
            ]);
        }

        return redirect()->route('unidades.index')
            ->with('success', 'Unidade atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $unidade = Unidade::findOrFail($id);
        
        // Verificar se existem usuários associados
        $usuariosAssociados = Unit::where('unit_uni_id', $id)->count();
        
        // Verificar se existem SelfCheckouts associados
        $selfsAssociados = Selfs::where('sel_uni_id', $id)->count();
        
        // Se houver usuários ou SelfCheckouts associados, não deixar fazer a exclusão
        if ($usuariosAssociados > 0 || $selfsAssociados > 0) {
            $errorMessage = 'Não é possível excluir esta unidade. ';
            
            if ($usuariosAssociados > 0 && $selfsAssociados > 0) {
                $errorMessage .= 'Existem usuários e SelfCheckouts/PDVs associados.';
            } elseif ($usuariosAssociados > 0) {
                $errorMessage .= 'Existem usuários associados.';
            } else {
                $errorMessage .= 'Existem SelfCheckouts/PDVs associados.';
            }
            
            return redirect()->route('unidades.index')
                ->with('error', $errorMessage);
        }
        
        // Se não houver associações, realizar a exclusão
        event(new UnidadeDeleteEvent("Codigo Unidade: " . $unidade->uni_codigo , request()->ip()));
        $unidade->delete();

        return redirect()->route('unidades.index')
            ->with('success', 'Unidade excluída com sucesso!');
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

        return view('unidades.index', compact('unidades'));
    }

    public function usuarios($id)
    {
        $unidade = Unidade::findOrFail($id);
        $usuarios = $unidade->todosUsuarios;
        
        return view('unidades.usuarios', compact('unidade', 'usuarios'));
    }

    public function processarUsuarios(Request $request, $id)
    {
        $unidade = Unidade::findOrFail($id);
        $usuariosParaAdicionar = json_decode($request->input('adicionar', '[]'), true);
        $usuariosParaRemover = json_decode($request->input('remover', '[]'), true);
        
        try {
            DB::beginTransaction();
            
            // Processar usuários para adicionar
            foreach ($usuariosParaAdicionar as $userId) {
                // Verificar se o vínculo já existe para evitar duplicidade
                $existingUnit = Unit::where('unit_uni_id', $id)
                    ->where('unit_use_id', $userId)
                    ->first();
                    
                if (!$existingUnit) {
                    // Cria o vínculo na tabela units
                    $unitNew = Unit::create([
                        'unit_uni_id' => $id,
                        'unit_use_id' => $userId
                    ]);
                    event(new UnitCreateEvent($unitNew->unit_id, request()->ip()));
                }
            }
            
            // Processar usuários para remover
            foreach ($usuariosParaRemover as $userId) {
                // Remove o vínculo da tabela units
                Unit::where('unit_uni_id', $id)
                    ->where('unit_use_id', $userId)
                    ->delete();
                event(new UnitDeleteEvent("User ID: " . $userId . " | Unidade ID: " . $id , request()->ip()));
            }
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('unidades.edit', $id)
                ->with('success', 'Usuários processados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->route('unidades.edit', $id)
                ->with('error', 'Erro ao processar usuários: ' . $e->getMessage());
        }
    }
}