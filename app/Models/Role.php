<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';
    protected $primaryKey = 'rol_id';

    protected $fillable = [
        'rol_name',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'use_rol_id', 'rol_id');
    }
}