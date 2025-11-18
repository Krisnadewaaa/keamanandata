<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class Pegawai extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pegawai';
    protected $primaryKey = 'ID_PEGAWAI';
    public $timestamps = false;

    protected $fillable = [
        'ID_ROLE',
        'NAMA_PEGAWAI',
        'EMAIL_PEGAWAI',
        'PASSWORD_PEGAWAI',
        'UANG_PEGAWAI',
        'ALAMAT_PEGAWAI',
        'NO_TELEPON_PEGAWAI',
        'TANGGAL_LAHIR',
        'ID_KOMISI'
    ];

    protected $hidden = [
        'PASSWORD_PEGAWAI',
    ];

    // Definisikan atribut tanggal agar Laravel otomatis mengubahnya menjadi instance Carbon
    protected $dates = [
        'TANGGAL_LAHIR'
    ];

    // Mutator untuk memastikan nilai yang disimpan selalu menjadi objek Carbon
    public function setTANGGALLAHIRAttribute($value)
    {
        $this->attributes['TANGGAL_LAHIR'] = $value ? Carbon::parse($value) : null;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'ID_ROLE', 'ID_ROLE');
    }

    public function komisi()
    {
        return $this->belongsTo(Komisi::class, 'ID_KOMISI', 'ID_KOMISI');
    }

    public function barangHunter()
    {
        return $this->hasMany(BarangHunter::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function donasis()
    {
        return $this->hasMany(Donasi::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    // Get password field name for Laravel authentication
    public function getAuthPassword()
    {
        return $this->PASSWORD_PEGAWAI;
    }

    // Get email field name for Laravel authentication
    public function getEmailForPasswordReset()
    {
        return $this->EMAIL_PEGAWAI;
    }
    
    // Check if the user is an owner
    public function isOwner()
    {
        return $this->ID_ROLE == 1;
    }
    
    // Check if the user is an admin
    public function isAdmin()
    {
        return $this->ID_ROLE == 2;
    }
    
    // Check if the user is a warehouse staff
    public function isGudang()
    {
        return $this->ID_ROLE == 3;
    }
    
    // Check if the user is a customer service
    public function isCS()
    {
        return $this->ID_ROLE == 4;
    }
    
    // Check if the user is a courier
    public function isKurir()
    {
        return $this->ID_ROLE == 5;
    }
    
    // Check if the user is a hunter
    public function isHunter()
    {
        return $this->ID_ROLE == 6;
    }

    /**
     * Check if the date of birth can be used as a password
     * 
     * @return bool
     */
    public function canUseDobAsPassword()
    {
        return !is_null($this->TANGGAL_LAHIR);
    }

    /**
     * Format date of birth as a password (ddmmyyyy)
     * 
     * @return string|null
     */
    public function getDobAsPassword()
    {
        if (!$this->canUseDobAsPassword()) {
            return null;
        }

        // Pastikan TANGGAL_LAHIR adalah instance Carbon
        if (is_string($this->TANGGAL_LAHIR)) {
            return Carbon::parse($this->TANGGAL_LAHIR)->format('dmY');
        }
        
        return $this->TANGGAL_LAHIR->format('dmY');
    }
}