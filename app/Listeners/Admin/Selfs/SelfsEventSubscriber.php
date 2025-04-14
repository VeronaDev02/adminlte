<?php

namespace App\Listeners\Admin\Selfs;

use App\Events\Admin\Selfs;
use App\Models\LogMessagesUser;
use Illuminate\Support\Facades\Log;

class SelfsEventSubscriber
{
    public function handleSelfsCreate($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleSelfsEdit($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleSelfsDelete($event)
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
            Selfs\Create::class => "handleSelfsCreate",
            Selfs\Edit::class => "handleSelfsEdit",
            Selfs\Delete::class => "handleSelfsDelete",
        ];
    }
}