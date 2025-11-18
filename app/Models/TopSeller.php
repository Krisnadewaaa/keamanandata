<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopSeller extends Model
{
    use HasFactory;

    protected $table = 'top_seller';
    protected $primaryKey = 'ID_TOP';
    public $timestamps = false;

    protected $fillable = [
        'ID_PENITIP',
        'ID_PENITIPAN',
        'JUMLAH_PENJUALAN'
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function penitipan()
    {
        return $this->belongsTo(Penitipan::class, 'ID_PENITIPAN', 'ID_PENITIPAN');
    }
}