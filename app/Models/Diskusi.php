<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diskusi extends Model
{
    use HasFactory;

    protected $table = 'diskusi';
    protected $primaryKey = 'ID_DISKUSI';
    public $timestamps = true;

    protected $fillable = [
        'ID_BARANG',
        'ID_PEMBELI',
        'ID_PEGAWAI',
        'ID_PARENT',
        'KOMENTAR',
        'NAMA_PENGIRIM',
        'IS_ADMIN'
    ];

    /**
     * Relasi dengan Barang
     * Menggunakan withDefault agar tidak null ketika barang sudah tidak ada
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG')->withDefault([
            'NAMA_BARANG' => 'Barang tidak tersedia',
            'DESKRIPSI' => 'Barang ini sudah tidak tersedia lagi di sistem',
            'HARGA' => 0,
            'STATUS' => 'Tidak Tersedia',
            'GARANSI' => 'Tidak',
        ]);
    }

    /**
     * Relasi dengan Pembeli
     */
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    /**
     * Relasi dengan Pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    /**
     * Relasi dengan Diskusi parent
     */
    public function parent()
    {
        return $this->belongsTo(Diskusi::class, 'ID_PARENT', 'ID_DISKUSI');
    }

    /**
     * Relasi dengan Diskusi replies
     */
    public function replies()
    {
        return $this->hasMany(Diskusi::class, 'ID_PARENT', 'ID_DISKUSI');
    }

    /**
     * Cek apakah diskusi merupakan parent diskusi
     */
    public function isParent()
    {
        return $this->ID_PARENT === null;
    }

    /**
     * Mendapatkan waktu dibuat dalam format yang mudah dibaca
     */
    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return \Carbon\Carbon::parse($value)->diffForHumans();
        }
        return null;
    }
}