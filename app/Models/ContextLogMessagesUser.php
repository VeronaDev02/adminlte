<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextLogMessagesUser extends Model
{
    use HasFactory;
    protected $table = "public.context_log_messages_user";
    protected $fillable = ["context"];
}
