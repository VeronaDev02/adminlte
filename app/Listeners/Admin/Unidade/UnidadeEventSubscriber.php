<?php

namespace App\Listeners\Admin\Unidade;

use App\Events\Admin\Unidade;
use App\Models\LogMessagesUser;
use Illuminate\Support\Facades\Log;

class UnidadeEventSubscriber
{
    public function handleUnidadeCreate($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleUnidadeEdit($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleUnidadeDelete($event)
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
            Unidade\Create::class => "handleUnidadeCreate",
            Unidade\Edit::class => "handleUnidadeEdit",
            Unidade\Delete::class => "handleUnidadeDelete",
        ];
    }
}