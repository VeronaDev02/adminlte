<?php

namespace App\Listeners\User\Auth;

use App\Events\User\Auth;
use App\Models\LogMessagesUser;
use Illuminate\Support\Facades\Log;

class UserAuthEventSubscriber
{
    public function handleUserLogin($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleUserLogout($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleFailedLoginAttempt($event)
    {
        Log::build([
            "driver" => "single",
            "path" => storage_path("logs/user.log"),
        ])->info(
            "O ip " .
                $event->ip .
                " tentou fazer login como usuario com o usuario " .
                $event->username
        );
        LogMessagesUser::create([
            "creator_username" => $event->username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function subscribe()
    {
        return [
            Auth\Login::class => "handleUserLogin",
            Auth\Logout::class => "handleUserLogout",
            Auth\FailedLoginAttempt::class => "handleFailedLoginAttempt",
        ];
    }
}
