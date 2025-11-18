<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlamatPembeli extends Model
{
    protected $table = 'alamat';
    protected $primaryKey = 'ID_ALAMAT';
    public $timestamps = false;

    protected $fillable = [
        'ID_PEMBELI',
        'ALAMAT_LENGKAP',
        'KOTA',
        'PROVINSI',
        'IS_DEFAULT'
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI');
    }
}
