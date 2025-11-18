<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangHunter extends Model
{
    use HasFactory;

    protected $table = 'barang_hunter';
    protected $primaryKey = 'ID_BARANGHUNTER';
    public $timestamps = false;

    protected $fillable = [
        'ID_PEGAWAI'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function penitipan()
    {
        return $this->hasMany(Penitipan::class, 'ID_BARANGHUNTER', 'ID_BARANGHUNTER');
    }

    public function barang()
    {
        // Relasi melalui tabel penitipan
        return $this->hasManyThrough(
            Barang::class,
            Penitipan::class,
            'ID_BARANGHUNTER', // Foreign key pada tabel penitipan
            'ID_PENITIPAN',    // Foreign key pada tabel barang
            'ID_BARANGHUNTER', // Local key pada tabel barang_hunter
            'ID_PENITIPAN'     // Local key pada tabel penitipan
        );
    }
}