<?php

namespace App\Events\User\Auth;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class Logout
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $username;
    public $creator;
    public $context;
    public $target;
    public $ip;

    public function __construct($username, $ip = "")
    {
        $this->username = $username;
        
        $user = User::where("use_username", strtolower($username))->first();
        
        if ($user) {
            $this->creator = $user->use_id;
        } else {
            error_log("ERRO: Usuário não encontrado para logout: $username");
            
            // Tente uma busca mais genérica caso a comparação de case esteja causando problemas
            $user = User::where('use_username', 'like', $username)->first();
            if ($user) {
                $this->creator = $user->id;
                error_log("Usuário encontrado com busca alternativa: {$user->use_username}");
            } else {
                // Lançar exceção para evitar inserção de nulo no banco
                throw new \Exception("Usuário não encontrado para evento de logout: $username");
            }
        }
        
        $this->context = 2;
        $this->target = null;
        $this->ip = $ip;
    }
}
