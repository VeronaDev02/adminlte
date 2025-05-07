<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_origin',
        'alert_resolved_by',
        'closed_at'
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];
}