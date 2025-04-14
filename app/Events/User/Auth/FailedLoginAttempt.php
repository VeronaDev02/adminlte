<?php

namespace App\Events\User\Auth;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FailedLoginAttempt
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $username;
    public $creator;
    public $context;
    public $target;
    public $ip;

    public function __construct($username, $ip)
    {
        $this->username = $username;
        $lowercase_username = strtolower($username);
        
        // Tenta encontrar o usuário pelo username
        $user = User::where("use_username", $lowercase_username)->first();
        
        if (!$user) {
            // Tenta uma busca case-insensitive
            $user = User::whereRaw('LOWER(use_username) = ?', [$lowercase_username])->first();
        }
        
        if ($user) {
            // Usuário existe, mas falhou no login (senha errada provavelmente)
            $this->creator = $user->use_id;
        } else {
            // Usuário não existe no sistema
            Log::warning("Tentativa de login com username inexistente: $username");
            
            // Já que o banco de dados não aceita 0 ou NULL, vamos usar um ID de sistema
            // Busca um usuário de sistema (por exemplo, admin ou um usuário específico para logs)
            $systemUser = User::where('use_username', 'admin')->first() ?? 
                         User::where('use_username', 'system')->first() ?? 
                         User::first(); // Como último recurso, pega o primeiro usuário
            
            if ($systemUser) {
                $this->creator = $systemUser->use_id;
            } else {
                // Se não encontrou nenhum usuário, loga o erro e joga uma exceção
                Log::error("Não foi possível encontrar um usuário de sistema para o log de tentativa de login falha");
                throw new \Exception("Não foi possível registrar tentativa de login: nenhum usuário de sistema encontrado");
            }
        }
        
        $this->context = 20;
        $this->target = null;
        $this->ip = $ip;
    }
}