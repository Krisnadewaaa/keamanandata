<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Penitip extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'penitip';
    protected $primaryKey = 'ID_PENITIP';
    public $timestamps = false;

    protected $fillable = [
        'ID_KOMISI',
        'NAMA_PENITIP',
        'EMAIL_PENITIP',
        'PASSWORD_PENITIP',
        'RATING_PENITIP',
        'UANG_PENITIP',
        'NO_KTP',
        'FOTO_KTP',
        'POIN_PENITIP',
        'FOTO_PROFIL'
    ];

    protected $hidden = [
        'PASSWORD_PENITIP',
    ];

    public function komisi()
    {
        return $this->belongsTo(Komisi::class, 'ID_KOMISI', 'ID_KOMISI');
    }
    
    public function penitipan()
    {
        return $this->hasMany(Penitipan::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function penitipans()
    {
        return $this->hasMany(Penitipan::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    // Get password field name for Laravel authentication
    public function getAuthPassword()
    {
        return $this->PASSWORD_PENITIP;
    }

    // Get email field name for Laravel authentication
    public function getEmailForPasswordReset()
    {
        return $this->EMAIL_PENITIP;
    }

   public function transaksiTerbaru($limit = 5)
{
    $penitipanIds = $this->penitipan()->pluck('ID_PENITIPAN');

    if ($penitipanIds->isEmpty()) {
        return collect(); // aman
    }

    $barangIds = Barang::whereIn('ID_PENITIPAN', $penitipanIds)->pluck('ID_BARANG');

    if ($barangIds->isEmpty()) {
        return collect(); // aman
    }

    return Transaksi::whereIn('ID_BARANG', $barangIds)
        ->where('STATUS_TRANSAKSI', 'Selesai')
        ->orderBy('TANGGAL_TRANSAKSI', 'desc')
        ->limit($limit)
        ->get();
}

// App\Models\Penitip.php

public function barangDititipkan()
{
    return $this->hasMany(Barang::class, 'ID_PENITIPAN', 'ID_PENITIP');
}

/**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'ID_PENITIP';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->ID_PENITIP;
    }

    /**
     * Get formatted money
     */
    public function getFormattedMoneyAttribute()
    {
        return 'Rp ' . number_format($this->UANG_PENITIP ?? 0, 0, ',', '.');
    }

    /**
     * Get total sales count
     */
    public function getTotalSalesAttribute()
    {
        return $this->penitipans()->where('STATUS_PENITIPAN', 'Selesai')->count();
    }

    /**
     * Get active consignments count
     */
    public function getActiveConsignmentsAttribute()
    {
        return $this->penitipans()->where('STATUS_PENITIPAN', 'Aktif')->count();
    }

    /**
     * Check if penitip has top seller status
     */
    public function getTopSellerAttribute()
    {
        return TopSeller::where('ID_PENITIP', $this->ID_PENITIP)->first();
    }
}
