<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Organisasi extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'organisasi';
    protected $primaryKey = 'ID_ORGANISASI';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_ORGANISASI',
        'EMAIL_ORGANISASI',
        'PASSWORD_ORGANISASI',
        'ALAMAT_ORGANISASI'
    ];

    protected $hidden = [
        'PASSWORD_ORGANISASI',
    ];

    public function donasis()
    {
        return $this->hasMany(Donasi::class, 'ID_ORGANISASI', 'ID_ORGANISASI');
    }

    // Get password field name for Laravel authentication
    public function getAuthPassword()
    {
        return $this->PASSWORD_ORGANISASI;
    }

    // Get email field name for Laravel authentication
    public function getEmailForPasswordReset()
    {
        return $this->EMAIL_ORGANISASI;
    }
}