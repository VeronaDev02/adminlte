<?php

namespace App\Http\Controllers\Admin;

use App\Events\Admin\Unidade\Create;
use App\Events\Admin\Unidade\Edit;
use App\Events\Admin\Unidade\Delete;
use App\Http\Controllers\Controller;
use App\Models\TipoUnidade;
use App\Models\Unidade;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UnidadeController extends Controller
{
    public function index()
    {
        $unidades = Unidade::orderBy('uni_codigo')->get();
        return view("admin.unidade.index", compact("unidades"));
    }

    public function create()
    {
        $tiposUnidade = TipoUnidade::orderBy('tip_nome')->get();
        $usuarios = User::orderBy('use_name')->get();
        
        return view("admin.unidade.create", compact("tiposUnidade", "usuarios"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'uni_codigo' => 'required|string|max:50|unique:unidade,uni_codigo',
            'uni_nome' => 'required|string|max:255',
            'uni_tip_id' => 'required|exists:tipo_unidade,tip_id',
            'uni_api' => 'required|string|max:255',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,use_id',
        ], [
            'uni_codigo.required' => 'O código da unidade é obrigatório.',
            'uni_codigo.unique' => 'Este código de unidade já está em uso.',
            'uni_nome.required' => 'O nome da unidade é obrigatório.',
            'uni_api.required' => 'A API da unidade é obrigatória.',
            'uni_tip_id.required' => 'O tipo de unidade é obrigatório.',
        ]);

        DB::beginTransaction();
        
        try {
            $unidade = Unidade::create([
                'uni_codigo' => $request->uni_codigo,
                'uni_nome' => $request->uni_nome,
                'uni_tip_id' => $request->uni_tip_id,
                'uni_api' => $request->uni_api,
            ]);
            
            if ($request->has('usuarios')) {
                $this->syncUsuarios($unidade, $request->usuarios);
            }
            
            DB::commit();
            
            event(new Create($unidade->uni_id, request()->ip()));

            return redirect()->route("admin.unidade.index")
                ->with("success", "Unidade criada com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log::error('Erro ao criar unidade: ' . $e->getMessage());
            
            return redirect()->back()
                ->with("error", "Erro ao cadastrar a unidade: " . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $unidade = Unidade::findOrFail($id);
        return view("admin.unidade.show", compact("unidade"));
    }

    public function edit($id)
    {
        $unidade = Unidade::findOrFail($id);
        $tiposUnidade = TipoUnidade::orderBy('tip_nome')->get();
        $usuarios = User::orderBy('use_name')->get();
        
        return view("admin.unidade.edit", compact("unidade", "tiposUnidade", "usuarios"));
    }

    public function update(Request $request, $id)
    {
        $unidade = Unidade::findOrFail($id);
        
        $request->validate([
            'uni_codigo' => 'required|string|max:50|unique:unidade,uni_codigo,'.$id.',uni_id',
            'uni_nome' => 'required|string|max:255',
            'uni_tip_id' => 'required|exists:tipo_unidade,tip_id',
            'uni_api' => 'required|string|max:255',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,use_id',
        ], [
            'uni_codigo.required' => 'O código da unidade é obrigatório.',
            'uni_codigo.unique' => 'Este código de unidade já está em uso.',
            'uni_nome.required' => 'O nome da unidade é obrigatório.',
            'uni_api.required' => 'A API da unidade é obrigatória.',
            'uni_tip_id.required' => 'O tipo de unidade é obrigatório.',
        ]);

        DB::beginTransaction();
        
        try {
            $unidade->update([
                'uni_codigo' => $request->uni_codigo,
                'uni_nome' => $request->uni_nome,
                'uni_tip_id' => $request->uni_tip_id,
                'uni_api' => $request->uni_api,
            ]);
            
            if ($request->has('usuarios')) {
                $this->syncUsuarios($unidade, $request->usuarios);
            } else {
                $this->syncUsuarios($unidade, []);
            }
            
            DB::commit();
            
            event(new Edit($unidade->uni_id, request()->ip()));

            return redirect()->route("admin.unidade.index")
                ->with("success", "Unidade atualizada com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log::error('Erro ao atualizar unidade: ' . $e->getMessage());
            
            return redirect()->back()
                ->with("error", "Erro ao atualizar a unidade: " . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $unidade = Unidade::findOrFail($id);
            $unidadeCodigo = $unidade->uni_codigo;
            
            if (count($unidade->users) > 0) {
                return redirect()->route("admin.unidade.index")
                    ->with("error", "Não foi possível excluir a unidade. Existem usuários vinculados a ela.");
            }
            
            if (count($unidade->selfs) > 0) {
                return redirect()->route("admin.unidade.index")
                    ->with("error", "Não foi possível excluir a unidade. Existem selfs vinculados a ela.");
            }
            
            Unit::where('unit_uni_id', $unidade->uni_id)->delete();
            
            $unidade->delete();
            
            DB::commit();
            
            event(new Delete("Código: " . $unidadeCodigo, request()->ip()));
            
            return redirect()->route("admin.unidade.index")
                ->with("success", "Unidade excluída com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log::error('Erro ao excluir unidade: ' . $e->getMessage());
            
            return redirect()->route("admin.unidade.index")
                ->with("error", "Erro ao excluir a unidade: " . $e->getMessage());
        }
    }

    public function getSelfs($id)
    {
        try {
            $unidade = Unidade::findOrFail($id);
            $selfs = $unidade->selfs;
            
            return response()->json([
                'status' => true,
                'data' => $selfs
            ]);
        } catch (\Exception $e) {
            // Log::error('Erro ao buscar selfs da unidade: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Erro ao buscar selfs da unidade: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsuarios($id)
    {
        try {
            $unidade = Unidade::findOrFail($id);
            $usuarios = $unidade->users;
            
            return response()->json([
                'status' => true,
                'data' => $usuarios
            ]);
        } catch (\Exception $e) {
            // Log::error('Erro ao buscar usuários da unidade: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Erro ao buscar usuários da unidade: ' . $e->getMessage()
            ], 500);
        }
    }

    private function syncUsuarios($unidade, $usuariosIds)
    {
        Unit::where('unit_uni_id', $unidade->uni_id)->delete();
        
        foreach ($usuariosIds as $usuarioId) {
            Unit::insert([
                'unit_use_id' => $usuarioId,
                'unit_uni_id' => $unidade->uni_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}