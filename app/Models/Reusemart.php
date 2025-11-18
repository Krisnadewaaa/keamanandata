<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reusemart extends Model
{
    use HasFactory;

    protected $table = 'reusemart';
    protected $primaryKey = 'ID_REUSEMART';
    public $timestamps = false;

    protected $fillable = [
        'ID_KOMISI',
        'TOTAL_PENDAPATAN'
    ];

    public function komisi()
    {
        return $this->belongsTo(Komisi::class, 'ID_KOMISI', 'ID_KOMISI');
    }
}