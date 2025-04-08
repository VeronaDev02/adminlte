<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unidade;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Exibe uma lista de todos os usuários.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('use_id', 'asc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Altera o status de um usuário (ativo/inativo)
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(User $user)
    {
        $user->use_active = !$user->use_active;
        $user->save();

        return response()->json([
            'status' => $user->use_active
        ]);
    }

    /**
     * Mostra o formulário para criar um novo usuário.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $unidades = Unidade::all();
        return view('admin.users.create', compact('roles', 'unidades'));
    }

    /**
     * Armazena um novo usuário no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'use_name' => 'required|string|max:255',
            'use_username' => 'required|string|max:50|unique:users,use_username',
            'use_email' => 'required|string|email|max:255|unique:users,use_email',
            'use_password' => 'required|string|min:6',
            'use_rol_id' => 'nullable|exists:role,rol_id',
            'use_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->all();
        
        // Assegurar que os valores booleanos sejam tratados corretamente
        $userData['use_active'] = $request->has('use_active') ? 1 : 0;
        $userData['use_login_ativo'] = $request->has('use_login_ativo') ? 1 : 0;
        $userData['use_allow_updates'] = $request->has('use_allow_updates') ? 1 : 0;

        // Criar o usuário
        $user = User::create($userData);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Exibe um usuário específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Mostra o formulário para editar um usuário existente.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $unidades = Unidade::all();
        
        // Busca todas as unidades que não estão associadas ao usuário através da tabela units
        $unidadesAssociadas = \App\Models\Unit::where('unit_use_id', $id)->pluck('unit_uni_id')->toArray();
        $unidadesDisponiveis = Unidade::whereNotIn('uni_id', $unidadesAssociadas)->get();
        
        return view('admin.users.edit', compact('user', 'roles', 'unidades', 'unidadesDisponiveis'));
    }

    /**
     * Atualiza um usuário específico no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $rules = [
            'use_name' => 'required|string|max:255',
            'use_username' => 'required|string|max:50|unique:users,use_username,' . $id . ',use_id',
            'use_email' => 'required|string|email|max:255|unique:users,use_email,' . $id . ',use_id',
            'use_rol_id' => 'nullable|exists:role,rol_id',
            'use_active' => 'boolean',
        ];

        // Se a senha foi fornecida, adicionar regra para validação
        if ($request->filled('use_password')) {
            $rules['use_password'] = 'string|min:6';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except(['use_password']);
        
        // Atualizar senha apenas se uma nova foi fornecida
        if ($request->filled('use_password')) {
            $userData['use_password'] = $request->use_password;
        }
        
        // Assegurar que os valores booleanos sejam tratados corretamente
        $userData['use_active'] = $request->has('use_active') ? 1 : 0;
        $userData['use_login_ativo'] = $request->has('use_login_ativo') ? 1 : 0;
        $userData['use_allow_updates'] = $request->has('use_allow_updates') ? 1 : 0;

        $user->update($userData);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove um usuário específico do banco de dados.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Excluir os relacionamentos na tabela unit
        \App\Models\Unit::where('unit_use_id', $id)->delete();
        
        // Excluir o usuário
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }

    /**
     * Busca usuários com base em critérios de filtro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = User::query();

        if ($request->has('name') && !empty($request->name)) {
            $query->where('use_name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('username') && !empty($request->username)) {
            $query->where('use_username', 'like', '%' . $request->username . '%');
        }

        if ($request->has('email') && !empty($request->email)) {
            $query->where('use_email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('role') && !empty($request->role)) {
            $query->where('use_rol_id', $request->role);
        }

        if ($request->has('active') && $request->active != '') {
            $query->where('use_active', $request->active);
        }

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Lista todas as unidades associadas a um usuário.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unidades($id)
    {
        $user = User::findOrFail($id);
        $unidades = $user->unidades;
        
        return view('admin.users.unidades', compact('user', 'unidades'));
    }

    /**
     * Adiciona uma unidade ao usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addUnidade(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $unidadeId = $request->input('unidade_id');
        
        try {
            // Verificar se o vínculo já existe para evitar duplicidade
            $existingUnit = \App\Models\Unit::where('unit_use_id', $id)
                ->where('unit_uni_id', $unidadeId)
                ->first();
                
            if (!$existingUnit) {
                // Cria o vínculo na tabela units
                \App\Models\Unit::create([
                    'unit_use_id' => $id,
                    'unit_uni_id' => $unidadeId
                ]);
            }
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('users.edit', $id)
                ->with('success', 'Unidade associada com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->route('users.edit', $id)
                ->with('error', 'Erro ao associar unidade: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma unidade do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeUnidade(Request $request, $id)
    {
        $unidadeId = $request->input('unidade_id');
        
        try {
            // Remove o vínculo da tabela units
            \App\Models\Unit::where('unit_use_id', $id)
                ->where('unit_uni_id', $unidadeId)
                ->delete();
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('users.edit', $id)
                ->with('success', 'Unidade removida com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->route('users.edit', $id)
                ->with('error', 'Erro ao remover unidade: ' . $e->getMessage());
        }
    }
}