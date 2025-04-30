<?php

namespace App\Http\Controllers\Admin;

use App\Events\Admin\User\Create;
use App\Events\Admin\User\Edit;
use App\Events\Admin\User\Delete;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('use_id')->get();
        return view("admin.user.index", compact("users"));
    }

    public function create()
    {
        $unidades = Unidade::orderBy('uni_codigo')->get();
        $roles = Role::orderBy('rol_name')->get();
        
        return view("admin.user.create", compact("unidades", "roles"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'use_name' => 'required|string|max:255',
            'use_username' => 'required|string|max:50|unique:users,use_username',
            'use_email' => 'nullable|string|email|max:255|unique:users,use_email',
            'use_cod_func' => 'required|string|max:50|unique:users,use_cod_func',
            'use_cpf' => 'required| string|unique:users,use_cpf',
            'use_rol_id' => 'required|exists:role,rol_id',
            'unidades' => 'required|array',
            'unidades.*' => 'exists:unidade,uni_id',
        ], [
            'use_name.required' => 'O nome do usuário é obrigatório.',
            'use_username.required' => 'O nome de usuário é obrigatório.',
            'use_username.unique' => 'Este nome de usuário já está em uso.',
            'use_email.email' => 'O email informado não é válido.',
            'use_email.unique' => 'Este email já está em uso.',
            'use_cod_func.required' => 'O código do funcionário é obrigatório.',
            'use_cod_func.unique' => 'Este código de funcionário já está em uso.',
            'use_cpf.required' => 'O CPF é obrigatório.',
            'use_cpf.unique' => 'Este CPF já está em uso.',
            'use_rol_id.required' => 'A função é obrigatória.',
            'unidades.required' => 'Pelo menos uma unidade é obrigatória.',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'use_name' => $request->use_name,
                'use_username' => $request->use_username,
                'use_email' => $request->use_email,
                'use_password' => 'senha123',
                'use_cell' => $request->use_cell,
                'use_rol_id' => $request->use_rol_id,
                'use_cod_func' => $request->use_cod_func,
                'use_cpf' => $request->use_cpf,
                'use_active' => $request->has('use_active'),
                'use_login_ativo' => $request->has('use_login_ativo'),
                'use_allow_updates' => $request->has('use_allow_updates'),
                'use_status_password' => false,
            ]);
            
            $this->syncUnidades($user, $request->unidades);
            
            DB::commit();
            
            event(new Create($user->use_id, request()->ip()));

            return redirect()->route("admin.user.index")
                ->with("success", "Usuário criado com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            
            return redirect()->back()
                ->with("error", "Erro ao cadastrar o usuário: " . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view("admin.user.show", compact("user"));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $unidades = Unidade::orderBy('uni_codigo')->get();
        $roles = Role::orderBy('rol_name')->get();
        
        return view("admin.user.edit", compact("user", "unidades", "roles"));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'use_name' => 'required|string|max:255',
            'use_username' => 'required|string|max:50|unique:users,use_username,'.$id.',use_id',
            'use_email' => 'nullable|string|email|max:255|unique:users,use_email,'.$id.',use_id',
            'use_cod_func' => 'required|string|max:50|unique:users,use_cod_func,'.$id.',use_id',
            'use_cpf' => 'required|string|unique:users,use_cpf,'.$id.',use_id',
            'use_rol_id' => 'required|exists:role,rol_id',
            'unidades' => 'required|array',
            'unidades.*' => 'exists:unidade,uni_id',
        ], [
            'use_name.required' => 'O nome do usuário é obrigatório.',
            'use_username.required' => 'O nome de usuário é obrigatório.',
            'use_username.unique' => 'Este nome de usuário já está em uso.',
            'use_email.email' => 'O email informado não é válido.',
            'use_email.unique' => 'Este email já está em uso.',
            'use_cod_func.required' => 'O código do funcionário é obrigatório.',
            'use_cod_func.unique' => 'Este código de funcionário já está em uso.',
            'use_cpf.required' => 'O CPF é obrigatório.',
            'use_cpf.unique' => 'Este CPF já está em uso.',
            'use_rol_id.required' => 'A função é obrigatória.',
            'unidades.required' => 'Pelo menos uma unidade é obrigatória.',
        ]);

        DB::beginTransaction();
        
        try {
            $userData = [
                'use_name' => $request->use_name,
                'use_username' => $request->use_username,
                'use_email' => $request->use_email,
                'use_cell' => $request->use_cell,
                'use_rol_id' => $request->use_rol_id,
                'use_cod_func' => $request->use_cod_func,
                'use_cpf' => $request->use_cpf,
                'use_active' => $request->has('use_active'),
                'use_login_ativo' => $request->has('use_login_ativo'),
                'use_allow_updates' => $request->has('use_allow_updates'),
            ];
            
            if ($request->filled('use_password')) {
                $userData['use_password'] = $request->use_password;
                $userData['use_status_password'] = false;
            }
            
            $user->update($userData);
            
            $this->syncUnidades($user, $request->unidades);
            
            DB::commit();
            
            event(new Edit($user->use_id, request()->ip()));

            return redirect()->route("admin.user.index")
                ->with("success", "Usuário atualizado com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
            
            return redirect()->back()
                ->with("error", "Erro ao atualizar o usuário: " . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $user = User::findOrFail($id);
            $userName = $user->use_username;
            
            // Remover registros na tabela units
            DB::table('units')->where('unit_use_id', $user->use_id)->delete();
            
            $user->delete();
            
            DB::commit();
            
            event(new Delete("Username: " . $userName, request()->ip()));
            
            return redirect()->route("admin.user.index")
                ->with("success", "Usuário excluído com sucesso.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao excluir usuário: ' . $e->getMessage());
            
            return redirect()->route("admin.user.index")
                ->with("error", "Erro ao excluir o usuário: " . $e->getMessage());
        }
    }

    public function getFuncionario(Request $request)
    {
        $codFuncionario = $request->input("use_cod_func");

        if (Cache::has("api_token")) {
            $token = Cache::get("api_token");
            if ($this->tokenExpirado()) {
                $token = $this->gerarToken();
            }
        } else {
            $token = $this->gerarToken();
        }

        if (!is_string($token)) {
            return $token;
        }

        $url = env("LINK_API") . $codFuncionario . "?token=" . $token;

        try {
            $response = Http::withOptions([
                "verify" => false,
            ])->get($url);

            if ($response->successful()) {
                $dados = $response->json();

                if (isset($dados["response"]["status"]) && $dados["response"]["status"] === "error") {
                    $messages = $dados["response"]["messages"] ?? [];
                    foreach ($messages as $message) {
                        if (isset($message["message"]) && strpos($message["message"], "NumberFormatException") !== false) {
                            return response()->json([
                                "error" => "Não encontrado funcionario com o código informado."
                            ], 400);
                        }
                    }
                    return response()->json([
                        "error" => "Erro ao buscar dados do funcionário."
                    ], 500);
                }

                if (isset($dados["response"]["funcionario"])) {
                    $nome = $dados["response"]["funcionario"]["nome"] ?? null;
                    $cpf = $dados["response"]["funcionario"]["cpf"] ?? null;
                    
                    return response()->json([
                        "nome" => $nome,
                        "cpf" => $cpf
                    ]);
                } else {
                    return response()->json([
                        "error" => "Formato de resposta inesperado",
                        "dados" => $dados
                    ], 500);
                }
            } else {
                return response()->json(
                    ["error" => "Erro na requisição."],
                    $response->status()
                );
            }
        } catch (\Exception $e) {
            Log::error('Erro na API: ' . $e->getMessage());
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

    private function syncUnidades($user, $unidadesIds)
    {
        DB::table('units')->where('unit_use_id', $user->use_id)->delete();
        
        foreach ($unidadesIds as $unidadeId) {
            DB::table('units')->insert([
                'unit_use_id' => $user->use_id,
                'unit_uni_id' => $unidadeId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function gerarToken()
    {
        $dados = [
            "usuario" => env("USUARIO_API"),
            "senha" => env("SENHA_API"),
        ];

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
            ])
                ->withOptions([
                    "verify" => false,
                ])
                ->post(env("LINK_AUTH_API"), $dados);

            if ($response->successful()) {
                $token = $response->json()["response"]["token"];

                Cache::put("api_token", $token, now()->addHours(1));
                Cache::put("api_token_expires_at", now()->addHours(1));

                return $token;
            } else {
                return response()->json(
                    ["error" => "Erro na requisição para a API"],
                    $response->status()
                );
            }
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

    private function tokenExpirado()
    {
        $expiration = Cache::get("api_token_expires_at");
        return now()->greaterThan($expiration);
    }
}