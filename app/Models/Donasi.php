<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasi';
    protected $primaryKey = 'ID_DONASI';
    public $timestamps = false;

    protected $fillable = [
        'ID_ORGANISASI',
        'ID_BARANG',
        'ID_PEGAWAI',
        'TANGGAL_DONASI',
        'ISI_REQUEST',
        'STATUS'
    ];

    protected $dates = [
        'TANGGAL_DONASI'
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'ID_ORGANISASI', 'ID_ORGANISASI');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'ID_PENITIP', 'ID_PENITIP');
    }
}