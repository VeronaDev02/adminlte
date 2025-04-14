<?php

namespace App\Listeners\Admin\Role;

use App\Events\Admin\Role;
use App\Models\LogMessagesUser;
use Illuminate\Support\Facades\Log;

class RoleEventSubscriber
{
    public function handleRoleCreate($event)
    {
        LogMessagesUser::create([
            "creator_usuario" => $event->usuario,
            "creator" => $event->creator,
            "creator_username" => $event->creator_username,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleRoleEdit($event)
    {
        LogMessagesUser::create([
            "creator_usuario" => $event->usuario,
            "creator" => $event->creator,
            "creator_username" => $event->creator_username,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleRoleDelete($event)
    {
        LogMessagesUser::create([
            "creator_usuario" => $event->usuario,
            "creator" => $event->creator,
            "creator_username" => $event->creator_username,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function subscribe($events)
    {
        return [
            Role\Create::class => "handleRoleCreate",
            Role\Edit::class => "handleRoleEdit",
            Role\Delete::class => "handleRoleDelete",
        ];
    }
}