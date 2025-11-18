<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komisi extends Model
{
    use HasFactory;

    protected $table = 'komisi';
    protected $primaryKey = 'ID_KOMISI';
    public $timestamps = false;

    protected $fillable = [
        'ID_TRANSAKSI',
        'TOTAL_KOMISI',
        'TANGGAL_KOMISI',
        'STATUS_KOMISI',
        'KOMISI_REUSEMART',
        'KOMISI_PEGAWAI',
        'KOMISI_PENITIP'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'ID_TRANSAKSI', 'ID_TRANSAKSI');
    }

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'ID_KOMISI', 'ID_KOMISI');
    }

    public function penitips()
    {
        return $this->hasMany(Penitip::class, 'ID_KOMISI', 'ID_KOMISI');
    }

    public function reusemart()
    {
        return $this->hasOne(Reusemart::class, 'ID_KOMISI', 'ID_KOMISI');
    }
}