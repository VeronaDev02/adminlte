<?php

namespace App\Events\Admin\Role;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class Edit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $usuario;
    public $creator;
    public $context;
    public $creator_username;
    public $target;
    public $ip;

    public function __construct($target, $ip)
    {
        $this->usuario = Auth::user()->usuario;
        $this->creator = Auth::user()->use_id;
        $this->creator_username = Auth::user()->use_username;
        $this->context = 9; // ID edição de função/cargo
        $this->target = $target;
        $this->ip = $ip;
    }
}