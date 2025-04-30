<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        // dd(session()->all());
        $roles = Role::orderBy('rol_id')->get();
        return view("admin.role.index", compact("roles"));
    }

    public function create()
    {
        return view("admin.role.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            'rol_name' => 'required|string|max:255|unique:role,rol_name',
        ], [
            'rol_name.required' => 'O nome da função é obrigatório.',
            'rol_name.unique' => 'Esta função já está cadastrada.',
        ]);

        try {
            $role = Role::create([
                'rol_name' => $request->rol_name,
            ]);

            return redirect()->route("admin.role.index")
                ->with("success", "Função criada com sucesso.");
        } catch (\Exception $e) {
            // Log::error('Erro ao criar função: ' . $e->getMessage());
            
            return redirect()->back()
                ->with("error", "Erro ao cadastrar a função: " . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return view("admin.role.show", compact("role"));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view("admin.role.edit", compact("role"));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'rol_name' => 'required|string|max:255|unique:role,rol_name,'.$id.',rol_id',
        ], [
            'rol_name.required' => 'O nome da função é obrigatório.',
            'rol_name.unique' => 'Esta função já está cadastrada.',
        ]);

        try {
            $role->update([
                'rol_name' => $request->rol_name,
            ]);

            return redirect()->route("admin.role.index")
                ->with("success", "Função atualizada com sucesso.");
        } catch (\Exception $e) {
            // Log::error('Erro ao atualizar função: ' . $e->getMessage());
            
            return redirect()->back()
                ->with("error", "Erro ao atualizar a função: " . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $roleName = $role->rol_name;
            
            $role->delete();
            
            return redirect()->route("admin.role.index")
                ->with("success", "Função excluída com sucesso.");
        } catch (\Exception $e) {
            // Log::error('Erro ao excluir função: ' . $e->getMessage());
            
            return redirect()->route("admin.role.index")
                ->with("error", "Não foi possível excluir a função. Existem usuários associados.");
        }
    }
}