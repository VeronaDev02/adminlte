<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'use_id';
    public $incrementing = true;
    public $username = 'use_username';
    public $password = 'use_password';

    protected $fillable = [
        'use_name',
        'use_email',
        'use_cod_func',
        'use_last_seen',
        'use_ip_origin',
        'use_username',
        'use_password',
        'use_cell',
        'use_active',
        'use_login_ativo',
        'use_allow_updates',
        'use_rol_id',
        'img_user',
        'use_status_password',
        'ui_preferences',
    ];

    protected $hidden = [
        'use_password',
        'remember_token',
    ];

    protected $casts = [
        'use_active' => 'boolean',
        'use_login_ativo' => 'boolean',
        'use_allow_updates' => 'boolean',
        'use_status_password' => 'boolean',
        'ui_preferences' => 'array',
    ];


    public function username()
    {
        return 'use_username';
    }

    public function getAuthPassword()
    {
        return $this->use_password;
    }

    public function setUsePasswordAttribute($value)
    {
        $this->attributes['use_password'] = bcrypt($value);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'use_rol_id', 'rol_id');
    }

    public function unidades()
    {
        return $this->belongsToMany(
            Unidade::class,
            'units',
            'unit_use_id',
            'unit_uni_id',
            'use_id',
            'uni_id'
        )->withTimestamps();
    }

    public function unidade()
    {
        return $this->unidades()->first();
    }

    public function getNameAttribute()
    {
        return $this->use_name;
    }

    public function getEmailAttribute()
    {
        return $this->use_email;
    }
    
    public function adminlte_profile_url()
    {
        return route('user.profile'); 
    }
    public function adminlte_image()
    {
        return null;
    }

    public function getUnidadesCodigoAttribute()
    {
        $unidadesIds = Unit::where('unit_use_id', $this->use_id)
        ->pluck('unit_uni_id')
        ->toArray();

        $unidadesCodigos = Unidade::whereIn('uni_id', $unidadesIds)
                ->pluck('uni_codigo')
                ->toArray();

        return $unidadesCodigos;
    }
}