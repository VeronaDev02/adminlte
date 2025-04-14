<?php

namespace App\Events\User\Auth;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class Login
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
        $this->creator = User::where("use_username", strtolower($username))->first()->use_id;
        $this->context = 1;
        $this->ip = $ip;
    }
}