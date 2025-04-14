<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use App\Events\Admin\User\Create as UserCreateEvent;
use App\Events\Admin\User\Edit as UserEditEvent;
use App\Events\Admin\User\Delete as UserDeleteEvent;
use App\Events\Admin\Unit\Create as UnitCreateEvent;
use App\Events\Admin\Unit\Delete as UnitDeleteEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unidade;
use App\Models\Role;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;



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
        event(new UserEditEvent($user->use_id, request()->ip()));

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
            'use_email' => 'nullable|string|email|max:255|unique:users,use_email',
            'use_password' => 'required|string|min:6',
            'use_rol_id' => 'required|exists:role,rol_id',
            'use_cod_func' => 'required|string|max:50|unique:users,use_cod_func',

        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $userData = $request->all();
            
            // Definir valores fixos para os campos booleanos
            $userData['use_active'] = $request->input('use_active', 0);
            $userData['use_login_ativo'] = $request->input('use_login_ativo', 0);
            $userData['use_allow_updates'] = 1;

            // Criar o usuário
            $user = User::create($userData);
            event(new UserCreateEvent($user->use_id, request()->ip()));

            // Processar unidades para adicionar
            $unidadesParaAdicionar = [];
            if ($request->filled('unidades_adicionar')) {
                $unidadesParaAdicionar = json_decode($request->input('unidades_adicionar'), true);
                
                foreach ($unidadesParaAdicionar as $unidadeId) {
                    // Verificar se o ID da unidade é válido
                    $unidade = Unidade::find($unidadeId);
                    
                    if ($unidade) {
                        // Criar o vínculo na tabela units
                        $unit = Unit::create([
                            'unit_use_id' => $user->use_id,
                            'unit_uni_id' => $unidadeId
                        ]);
                        event(new UnitCreateEvent($unit->unit_id, request()->ip()));
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('users.index')
                ->with('success', 'Usuário criado com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('users.create')
                ->with('error', 'Erro ao criar usuário: ' . $e->getMessage())
                ->withInput();
        }
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
        $unidadesAssociadas = Unit::where('unit_use_id', $id)->pluck('unit_uni_id')->toArray();
        $unidadesDisponiveis = Unidade::whereNotIn('uni_id', $unidadesAssociadas)->get();
        
        // Busca as unidades associadas ao usuário
        $userUnidades = Unit::where('unit_use_id', $id)
            ->join('unidade', 'unit_uni_id', '=', 'uni_id')
            ->select('unidade.*')
            ->get();
        
        return view('admin.users.edit', compact('user', 'roles', 'unidades', 'unidadesDisponiveis', 'userUnidades'));
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
        
        // Check if this is a validation-only request
        if ($request->input('validate_only')) {
            return $this->validateUniqueFields($request, $id);
        }
        
        $rules = [
            'use_name' => 'required|string|max:255',
            'use_username' => 'required|string|max:50|unique:users,use_username,' . $id . ',use_id',
            'use_email' => 'nullable|string|email|max:255|unique:users,use_email,' . $id . ',use_id',
            'use_rol_id' => 'required|exists:role,rol_id',
            'use_cod_func' => 'required|string|max:50|unique:users,use_cod_func,' . $id . ',use_id',
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

        $userData = [
            'use_name' => $request->use_name,
            'use_username' => $request->use_username,
            'use_email' => $request->use_email,
            'use_rol_id' => $request->use_rol_id,
            'use_cod_func' => $request->use_cod_func,
            'use_cell' => $request->use_cell
        ];

        if ($request->filled('use_password')) {
            $userData['use_password'] = $request->use_password;
        }
        
        // Atualizar senha apenas se uma nova foi fornecida
        if ($request->filled('use_password')) {
            $userData['use_password'] = $request->use_password;
        }
        
        // Assegurar que os valores booleanos sejam tratados corretamente
        $userData['use_active'] = $request->has('use_active') ? 1 : 0;
        $userData['use_login_ativo'] = $request->has('use_login_ativo') ? 1 : 0;
        $userData['use_allow_updates'] = $request->has('use_allow_updates') ? 1 : 0;

         // Processar unidades
        $unidadesParaAdicionar = json_decode($request->input('unidades_adicionar', '[]'), true);
        $unidadesParaRemover = json_decode($request->input('unidades_remover', '[]'), true);

        try {
            DB::beginTransaction();

            // Atualizar usuário
            $user->update($userData);
            event(new UserEditEvent($user->use_id, request()->ip()));

            $unidadesAtuais = $user->unidades()->pluck('unit_uni_id')->toArray();
            $unidadesNovas = array_diff($unidadesParaAdicionar, $unidadesAtuais);
            $unidadesRemover = array_intersect($unidadesParaRemover, $unidadesAtuais);

            // Processar unidades para adicionar
            foreach ($unidadesNovas as $unidadeId) {
                $unit = Unit::create([
                    'unit_use_id' => $id,
                    'unit_uni_id' => $unidadeId
                    
                ]);
                event(new UnitCreateEvent($unit->unit_id, request()->ip()));
            }
            if(count($unidadesRemover) != 0) {
                Unit::where('unit_use_id', $id)
                    ->whereIn('unit_uni_id', $unidadesRemover)
                    ->delete();
                event(new UnitDeleteEvent("User ID: " . $id . " | Unidades ID: " . implode(', ', $unidadesRemover), request()->ip()));
            }
            
            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('users.edit', $id)
                ->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Método auxiliar para validação única de campos
    private function validateUniqueFields(Request $request, $currentUserId)
    {
        $errors = [];

        // Validar username
        $existingUsername = User::where('use_username', $request->input('use_username'))
            ->where('use_id', '!=', $currentUserId)
            ->exists();
        if ($existingUsername) {
            $errors['use_username'] = 'O nome de usuário já está em uso.';
        }

        // Validar email (se fornecido)
        if ($request->filled('use_email')) {
            $existingEmail = User::where('use_email', $request->input('use_email'))
                ->where('use_id', '!=', $currentUserId)
                ->exists();
            if ($existingEmail) {
                $errors['use_email'] = 'O email já está em uso.';
            }
        }

        // Validar código do funcionário
        $existingCodFunc = User::where('use_cod_func', $request->input('use_cod_func'))
            ->where('use_id', '!=', $currentUserId)
            ->exists();
        if ($existingCodFunc) {
            $errors['use_cod_func'] = 'O código do funcionário já está em uso.';
        }

        // Responder com resultado da validação
        return response()->json([
            'valid' => count($errors) === 0,
            'errors' => $errors
        ]);
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
        Unit::where('unit_use_id', $id)->delete();
        event(new UserDeleteEvent("Username: " . $user->use_username, request()->ip()));
        // Excluir o usuário
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }

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

    public function unidades($id)
    {
        $user = User::findOrFail($id);
        $unidades = $user->unidades;
        
        return view('admin.users.unidades', compact('user', 'unidades'));
    }

    public function processarUnidades(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $unidadesParaAdicionar = json_decode($request->input('adicionar', '[]'), true);
        $unidadesParaRemover = json_decode($request->input('remover', '[]'), true);
        
        try {
            DB::beginTransaction();
            
            // Processar unidades para adicionar
            foreach ($unidadesParaAdicionar as $unidadeId) {
                // Verificar se o vínculo já existe para evitar duplicidade
                $existingUnit = Unit::where('unit_use_id', $id)
                    ->where('unit_uni_id', $unidadeId)
                    ->first();
                    
                if (!$existingUnit) {
                    // Cria o vínculo na tabela units
                    Unit::create([
                        'unit_use_id' => $id,
                        'unit_uni_id' => $unidadeId
                    ]);
                }
            }
            
            // Processar unidades para remover
            foreach ($unidadesParaRemover as $unidadeId) {
                // Remove o vínculo da tabela units
                Unit::where('unit_use_id', $id)
                    ->where('unit_uni_id', $unidadeId)
                    ->delete();
            }
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('users.edit', $id)
                ->with('success', 'Unidades processadas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            
            return redirect()->route('users.edit', $id)
                ->with('error', 'Erro ao processar unidades: ' . $e->getMessage());
        }
    }
    public function getFuncionario(Request $request)
    {
        $codFuncionario = $request->input('use_cod_func');
        Log::info("Buscando funcionário com código: " . $codFuncionario);

        if (Cache::has('api_token')) {
            $token = Cache::get('api_token');
            if ($this->tokenExpirado()) {
                $token = $this->gerarToken();
            }
        } else {
            $token = $this->gerarToken();
        }

        if (!is_string($token)) {
            return $token;
        }

        $url = env('LINK_API') . $codFuncionario . '?token=' . $token;

        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->get($url);

            if ($response->successful()) {
                $dados = $response->json();
                Log::info('Resposta da API:', $dados);

                if (isset($dados['response']['status']) && $dados['response']['status'] === 'error') {
                    $messages = $dados['response']['messages'] ?? [];
                    foreach ($messages as $message) {
                        if (isset($message['message']) && strpos($message['message'], 'NumberFormatException') !== false) {
                            return response()->json([
                                'error' => 'Não encontrado funcionario com o código informado.'
                            ], 400);
                        }
                    }
                    return response()->json([
                        'error' => 'Erro ao buscar dados do funcionário.'
                    ], 500);
                }

                if (isset($dados['response']) && isset($dados['response']['funcionario'])) {
                    $nome = $dados['response']['funcionario']['nome'] ?? null;
                    
                    return response()->json([
                        'name' => $nome,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Formato de resposta inesperado',
                        'dados' => $dados
                    ], 500);
                }
            } else {
                return response()->json(['error' => 'Erro na requisição.', 'status' => $response->status()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function gerarToken()
    {
        $dados = [
            'usuario' => env('USUARIO_API'),
            'senha' => env('SENHA_API'),
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withOptions([
                'verify' => false,
            ])
            ->post(env('LINK_AUTH_API'), $dados);

            if ($response->successful()) {
                $token = $response->json()['response']['token'];

                Cache::put('api_token', $token, now()->addHours(1));
                Cache::put('api_token_expires_at', now()->addHours(1));

                return $token;
            } else {
                return response()->json(
                    ['error' => 'Erro na requisição para a API', 'status' => $response->status()],
                    $response->status()
                );
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function tokenExpirado()
    {
        $expiration = Cache::get('api_token_expires_at');
        return now()->greaterThan($expiration);
    }
}