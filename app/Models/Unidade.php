<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    use HasFactory;

    protected $table = 'unidade';
    protected $primaryKey = 'uni_id';

    protected $fillable = [
        'uni_codigo',
        'uni_tip_id',
        'uni_nome'
    ];

    public function tipoUnidade()
    {
        return $this->belongsTo(TipoUnidade::class, 'uni_tip_id', 'tip_id');
    }

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

    public function selfs()
    {
        return $this->hasMany(Selfs::class, 'sel_uni_id', 'uni_id');
    }

    public function getNomeAttribute()
    {
        return $this->tipoUnidade->tip_nome . ' - ' . $this->uni_nome;
    }

    public function getUnitIdsAttribute()
    {
        return Unit::where('unit_uni_id', $this->uni_id)->pluck('unit_id')->toArray();
    }

    public function getUseIdsAttribute()
    {
        return Unit::where('unit_uni_id', $this->uni_id)->pluck('unit_use_id')->toArray();
    }
}