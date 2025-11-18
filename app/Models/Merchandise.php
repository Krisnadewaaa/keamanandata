<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model
{
    use HasFactory;

    protected $table = 'merchandise';
    protected $primaryKey = 'ID_MERCHANDISE';
    public $timestamps = false;

    protected $fillable = [
        'POINT',
        'JENIS_MERCHANDISE'
    ];

    public function pointRewards()
    {
        return $this->hasMany(PointReward::class, 'ID_MERCHANDISE', 'ID_MERCHANDISE');
    }
}