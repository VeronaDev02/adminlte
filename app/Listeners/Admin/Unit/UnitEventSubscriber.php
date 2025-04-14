<?php

namespace App\Listeners\Admin\Unit;

use App\Events\Admin\Unit;
use App\Models\LogMessagesUser;
use Illuminate\Support\Facades\Log;

class UnitEventSubscriber
{
    public function handleUnitCreate($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleUnitEdit($event)
    {
        LogMessagesUser::create([
            "creator_username" => $event->creator_username,
            "creator" => $event->creator,
            "context" => $event->context,
            "target" => $event->target,
            "ip_origin" => $event->ip,
        ]);
    }

    public function handleUnitDelete($event)
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
            Unit\Create::class => "handleUnitCreate",
            Unit\Edit::class => "handleUnitEdit",
            Unit\Delete::class => "handleUnitDelete",
        ];
    }
}