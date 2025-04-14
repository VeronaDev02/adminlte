<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selfs extends Model
{
    use HasFactory;

    protected $table = 'selfs';
    protected $primaryKey = 'sel_id';

    protected $fillable = [
        'sel_name',
        'sel_pdv_ip',
        'sel_rtsp_url',
        'sel_status',  
        'sel_uni_id',
    ];

    protected $casts = [
        'sel_status' => 'boolean',
    ];

    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'sel_uni_id', 'uni_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('sel_status', true);
    }
    
    public function scopeInactive($query)
    {
        return $query->where('sel_status', false);
    }
}