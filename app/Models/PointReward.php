<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointReward extends Model
{
    use HasFactory;

    protected $table = 'point_reward';
    protected $primaryKey = 'ID_POINT';
    public $timestamps = false;

    protected $fillable = [
        'ID_PEMBELI',
        'ID_MERCHANDISE',
        'JUMLAH_POINT',
        'TANGGAL_AMBIL',
        'STATUS_KLAIM'
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class, 'ID_MERCHANDISE', 'ID_MERCHANDISE');
    }
}