<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';
    protected $primaryKey = 'ID_ROLE';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_ROLE'
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'ID_ROLE', 'ID_ROLE');
    }
}