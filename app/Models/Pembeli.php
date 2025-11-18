<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pembeli extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pembeli';
    protected $primaryKey = 'ID_PEMBELI';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_PEMBELI',
        'EMAIL_PEMBELI',
        'PASSWORD_PEMBELI',
        'POINT_PEMBELI',
        'ID_BARANG',    
        'FOTO_PROFIL'
    ];

    protected $hidden = [
        'PASSWORD_PEMBELI',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function keranjangs()
    {
        return $this->hasMany(KeranjangPembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function pointReward()
    {
        return $this->hasOne(PointReward::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    // Get password field name for Laravel authentication
    public function getAuthPassword()
    {
        return $this->PASSWORD_PEMBELI;
    }

    // Get email field name for Laravel authentication
    public function getEmailForPasswordReset()
    {
        return $this->EMAIL_PEMBELI;
    }
    
    public function cartItems()
    {
        return $this->hasMany(KeranjangPembeli::class, 'ID_PEMBELI', 'ID_PEMBELI')
                    ->whereDoesntHave('transaksi');
    }

    /**
     * Get cart count
     */
    public function getCartCountAttribute()
    {
        return $this->cartItems()->count();
    }

    /**
     * Get formatted points
     */
    public function getFormattedPointsAttribute()
    {
        return number_format($this->POINT_PEMBELI, 0, ',', '.');
    }

    /**
     * Add points to the buyer
     */
    public function addPoints($points)
    {
        $this->POINT_PEMBELI = $this->POINT_PEMBELI + $points;
        $this->save();
        return $this;
    }

    /**
     * Remove points from the buyer
     */
    public function removePoints($points)
    {
        $this->POINT_PEMBELI = max(0, $this->POINT_PEMBELI - $points);
        $this->save();
        return $this;
    }

    /**
     * Calculate potential points from a purchase amount
     */
    public static function calculatePotentialPoints($amount)
    {
        $points = floor($amount / 10000);
        
        // 20% bonus points for purchases over 500,000
        if ($amount > 500000) {
            $points = ceil($points * 1.2);
        }
        
        return $points;
    }

    // app/Models/Pembeli.php
    public function alamat()
    {
        return $this->hasOne(AlamatPembeli::class, 'ID_PEMBELI', 'ID_PEMBELI')->where('IS_DEFAULT', true);
    }

}