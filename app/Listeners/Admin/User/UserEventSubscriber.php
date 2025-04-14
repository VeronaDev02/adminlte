<?php

namespace App\Listeners\Admin\User;

use App\Events\Admin\User;
use App\Models\LogMessagesUser;
use Illuminate\Support\Facades\Log;

class UserEventSubscriber
{
    public function handleUserCreate($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleUserEdit($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleUserDelete($event)
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
            User\Create::class => "handleUserCreate",
            User\Edit::class => "handleUserEdit",
            User\Delete::class => "handleUserDelete",
        ];
    }
}