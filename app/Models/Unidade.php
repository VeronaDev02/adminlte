<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    use HasFactory;

    /**
     * Nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'unidade';

    /**
     * Chave primária da tabela.
     *
     * @var string
     */
    protected $primaryKey = 'uni_id';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'uni_codigo',
        'uni_descricao',
        'uni_cidade',
        'uni_uf',
    ];

    /**
     * Relacionamento com usuários
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'units',
            'unit_uni_id',
            'unit_use_id',
            'uni_id',
            'use_id'
        );
    }

    /**
     * Relacionamento com selfs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selfs()
    {
        return $this->hasMany(Selfs::class, 'sel_uni_id', 'uni_id');
    }

    /**
     * Relacionamento many-to-many com users através da tabela unit_user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    /**
 * Relacionamento many-to-many com users através da tabela units
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
 */
}