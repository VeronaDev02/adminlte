<?php

namespace App\Models;

use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUnidade extends Model
{
    use HasFactory;

    protected $table = 'tipo_unidade';
    protected $primaryKey = 'tip_id';

    protected $fillable = [
        'tip_codigo',
        'tip_nome',
        'tip_cor'
    ];

    public function unidades()
    {
        return $this->hasMany(Unidade::class, 'uni_tip_id', 'tip_id');
    }
}