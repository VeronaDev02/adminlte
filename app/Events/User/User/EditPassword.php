<?php

namespace App\Events\User\User;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class EditPassword
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $usuario;
    public $creator;
    public $context;
    public $target;
    public $creator_username;
    public $ip;

    public function __construct($target, $ip)
    {
        $this->usuario = Auth::user()->usuario;
        $this->creator = Auth::user()->use_id;
        $this->creator_username = Auth::user()->use_username;
        $this->context = 6; // ID correspondente à alteração de senha de usuário
        $this->target = $target;
        $this->ip = $ip;
    }
}