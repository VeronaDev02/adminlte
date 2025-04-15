<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function edit($id)
    {
        return view('admin.users.edit', ['userId' => $id]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user)
    {
        $user->use_active = !$user->use_active;
        $user->save();
        event(new \App\Events\Admin\User\Edit($user->use_id, request()->ip()));

        return response()->json([
            'status' => $user->use_active
        ]);
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

    public function validateUniqueFields(Request $request, $currentUserId)
    {
        $errors = [];

        $existingUsername = User::where('use_username', $request->input('use_username'))
            ->where('use_id', '!=', $currentUserId)
            ->exists();
        if ($existingUsername) {
            $errors['use_username'] = 'O nome de usuário já está em uso.';
        }

        if ($request->filled('use_email')) {
            $existingEmail = User::where('use_email', $request->input('use_email'))
                ->where('use_id', '!=', $currentUserId)
                ->exists();
            if ($existingEmail) {
                $errors['use_email'] = 'O email já está em uso.';
            }
        }

        $existingCodFunc = User::where('use_cod_func', $request->input('use_cod_func'))
            ->where('use_id', '!=', $currentUserId)
            ->exists();
        if ($existingCodFunc) {
            $errors['use_cod_func'] = 'O código do funcionário já está em uso.';
        }

        return response()->json([
            'valid' => count($errors) === 0,
            'errors' => $errors
        ]);
    }
    private function fetchFuncionarioDetails($codFuncionario)
    {
        $token = $this->gerarToken();
        
        $url = env('LINK_API') . $codFuncionario . '?token=' . $token;

        $response = Http::withOptions([
            'verify' => false,
        ])->get($url);

        if ($response->successful()) {
            $dados = $response->json();
            
            if (isset($dados['response']['funcionario']['nome'])) {
                return [
                    'name' => $dados['response']['funcionario']['nome']
                ];
            }
        }

        throw new \Exception('Funcionário não encontrado');
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