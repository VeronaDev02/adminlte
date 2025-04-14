<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LogMessagesUser extends Model
{
    use HasFactory;
    protected $table = "public.log_messages_user";
    protected $fillable = [
        "creator_username",
        "creator",
        "context",
        "target",
        "ip_origin",
        "mac_address",
    ];

    protected $casts = [
        "last_seen" => "datetime",
    ];

    public function contexto()
    {
        return $this->belongsTo(ContextLogMessagesUser::class, "context");
    }

    public function getCreatedAt($value)
    {
        $lastActivity = Carbon::parse($value);

        $now = Carbon::now();

        $difference = $lastActivity->diffForHumans($now);

        $translatedDifference = str_replace("antes", "atrás", $difference);

        return "Há " . $translatedDifference;
    }
}
