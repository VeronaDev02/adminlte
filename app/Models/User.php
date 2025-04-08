<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Chave primária da tabela.
     *
     * @var string
     */
    protected $primaryKey = 'use_id';

    /**
     * Indica se a chave primária é auto-incremento.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Coluna utilizada para autenticação (login).
     *
     * @var string
     */
    public $username = 'use_username';

    /**
     * Coluna utilizada para a senha.
     *
     * @var string
     */
    public $password = 'use_password';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
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
    ];

    /**
     * Os atributos que devem ser escondidos em arrays.
     *
     * @var array
     */
    protected $hidden = [
        'use_password',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'use_active' => 'boolean',
        'use_login_ativo' => 'boolean',
        'use_allow_updates' => 'boolean',
    ];

    /**
     * Função para autenticação - informa qual atributo usar para login
     *
     * @return string
     */
    public function username()
    {
        return 'use_username';
    }

    /**
     * Modifica o comportamento da autenticação para usar o campo use_password
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->use_password;
    }

    /**
     * Setter para o atributo password - com hash automático
     *
     * @param string $value
     * @return void
     */
    public function setUsePasswordAttribute($value)
    {
        $this->attributes['use_password'] = bcrypt($value);
    }

    /**
     * Relacionamento com a role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'use_rol_id', 'rol_id');
    }

    /**
     * Relacionamento many-to-many com unidades através da tabela units
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
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

    /**
     * Obtém a unidade principal do usuário (a primeira associada)
     *
     * @return \App\Models\Unidade|null
     */
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
        return route('perfil'); // Supondo que você tenha uma rota chamada 'perfil'
    }
}