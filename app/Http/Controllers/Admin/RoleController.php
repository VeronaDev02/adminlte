<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Exibe uma lista de todos os roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('rol_id', 'asc')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Mostra o formulário para criar um novo role.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Armazena um novo role no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rol_name' => 'required|string|max:255|unique:role,rol_name'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Role::create([
            'rol_name' => $request->rol_name
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role criado com sucesso!');
    }

    /**
     * Exibe um role específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Mostra o formulário para editar um role existente.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        
        // Busca usuários que não estão associados a este role
        $usuariosDisponiveis = User::where('use_rol_id', '!=', $id)
            ->orWhereNull('use_rol_id')
            ->get();
        
        // Para cada usuário, buscar sua unidade
        $role->users->each(function($user) {
            $user->unidade = $user->unidade();
        });

        return view('admin.roles.edit', compact('role', 'usuariosDisponiveis'));
    }

    /**
     * Atualiza um role existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'rol_name' => 'required|string|max:255|unique:role,rol_name,' . $id . ',rol_id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'rol_name' => $request->rol_name
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role atualizado com sucesso!');
    }

    /**
     * Remove um role do banco de dados.
     * Realiza exclusão em cascata de todos os usuários associados.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Excluir todos os usuários associados a este role
        $role->users()->delete();
        
        // Finalmente excluir o role
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role excluído com sucesso!');
    }

    /**
     * Obtém todos os usuários associados a um role específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUsers($id)
    {
        $role = Role::findOrFail($id);
        $users = $role->users;
        
        return view('admin.roles.users', compact('role', 'users'));
    }

    /**
     * Busca roles com base em critérios de filtro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Role::query();

        if ($request->has('name') && !empty($request->name)) {
            $query->where('rol_name', 'like', '%' . $request->name . '%');
        }

        $roles = $query->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Adiciona um usuário ao role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addUser(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $userId = $request->input('user_id');
        
        try {
            // Atualiza o role do usuário
            $user = User::findOrFail($userId);
            $user->use_rol_id = $id;
            $user->save();
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('roles.edit', $id)
                ->with('success', 'Usuário associado com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->route('roles.edit', $id)
                ->with('error', 'Erro ao associar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Remove um usuário do role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Request $request, $id)
    {
        $userId = $request->input('user_id');
        
        try {
            // Remove o role do usuário (define como null)
            $user = User::findOrFail($userId);
            $user->use_rol_id = null;
            $user->save();
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('roles.edit', $id)
                ->with('success', 'Usuário removido com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->route('roles.edit', $id)
                ->with('error', 'Erro ao remover usuário: ' . $e->getMessage());
        }
    }
}