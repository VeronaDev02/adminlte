<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Selfs extends Model
{
    use HasFactory;

    protected $table = 'selfs';
    protected $primaryKey = 'sel_id';

    protected $fillable = [
        'sel_name',
        'sel_pdv_ip',
        'sel_dvr_ip',
        'sel_dvr_username',
        'sel_dvr_password',
        'sel_camera_canal',
        'sel_dvr_porta',
        'sel_rtsp_path',
        'sel_status',  
        'sel_uni_id',
    ];

    protected $casts = [
        'sel_status' => 'boolean',
    ];

    // Mutators para campos que precisam ser criptografados
    public function setSelPdvIpAttribute($value)
    {
        $this->attributes['sel_pdv_ip'] = Crypt::encryptString($value);
    }

    public function getSelPdvIpAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setSelDvrIpAttribute($value)
    {
        $this->attributes['sel_dvr_ip'] = Crypt::encryptString($value);
    }

    public function getSelDvrIpAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setSelDvrUsernameAttribute($value)
    {
        $this->attributes['sel_dvr_username'] = Crypt::encryptString($value);
    }

    public function getSelDvrUsernameAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setSelDvrPasswordAttribute($value)
    {
        $this->attributes['sel_dvr_password'] = Crypt::encryptString($value);
    }

    public function getSelDvrPasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setSelRtspPathAttribute($value)
    {
        $truncatedValue = mb_substr($value, 0, 250);
        $this->attributes['sel_rtsp_path'] = Crypt::encryptString($truncatedValue);
    }

    public function getSelRtspPathAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Método para gerar URL RTSP
    public function getRtspUrlAttribute()
    {
        // Verifica se todos os campos necessários estão preenchidos
        if (!$this->sel_dvr_username || 
            !$this->sel_dvr_password || 
            !$this->sel_dvr_ip || 
            !$this->sel_dvr_porta || 
            !$this->sel_rtsp_path ||
            !$this->sel_camera_canal) {
            return null;
        }

        // Criptografa a URL RTSP completa
        return Crypt::encryptString(sprintf(
            'rtsp://%s:%s@%s:%s/%s?channel=%s&subtype=0',
            $this->sel_dvr_username,
            $this->sel_dvr_password,
            $this->sel_dvr_ip,
            $this->sel_dvr_porta,
            $this->sel_rtsp_path,
            $this->sel_camera_canal
        ));
    }

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