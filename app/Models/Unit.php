<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    use HasFactory;
    protected $table = 'units';
    protected $primaryKey = 'unit_id';

    protected $fillable = [
        'unit_uni_id', 
        'unit_use_id'
    ];

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class, 'unit_uni_id', 'uni_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unit_use_id', 'use_id');
    }
}