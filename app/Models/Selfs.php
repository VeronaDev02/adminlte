<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selfs extends Model
{
    use HasFactory;

    /**
     * Nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'selfs';

    /**
     * Chave primária da tabela.
     *
     * @var string
     */
    protected $primaryKey = 'sel_id';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'sel_name',
        'sel_pdv_ip',
        'sel_rtsp_url',
        'sel_status',  // Adicionado o campo status
        'sel_uni_id',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'sel_status' => 'boolean',
    ];

    /**
     * Relacionamento com a unidade
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'sel_uni_id', 'uni_id');
    }
    
    /**
     * Escopo para PDVs ativos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('sel_status', true);
    }
    
    /**
     * Escopo para PDVs inativos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('sel_status', false);
    }
}