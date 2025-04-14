<?php

namespace App\Listeners\User\User;

use App\Events\User\User;
use App\Models\LogMessagesUser;
use Illuminate\Support\Facades\Log;

class UserEventSubscriber /* implements ShouldQueue */
{
    public function handleUserEditPassword($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleUserEditPerfil($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function subscribe($events)
    {
        return [
            User\EditPassword::class => "handleUserEditPassword",
            User\EditPerfil::class => "handleUserEditPerfil",
        ];
    }
}