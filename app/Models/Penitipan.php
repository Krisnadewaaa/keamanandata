<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Penitipan extends Model
{
    use HasFactory;

    protected $table = 'penitipan';
    protected $primaryKey = 'ID_PENITIPAN';
    public $timestamps = false;

    protected $fillable = [
        'ID_PENITIP',
        'ID_BARANG',
        'ID_BARANGHUNTER',
        'ID_PEGAWAI',
        'CREATED_BY',
        'CREATED_BY_NAME',
        'CREATED_AT_FORMATTED',
        'UPDATED_BY',
        'UPDATED_BY_NAME',
        'UPDATED_AT_FORMATTED',
        'TANGGAL_MULAI',
        'TANGGAL_BERAKHIR',
        'STATUS_PENITIPAN',
        'TANGGAL_UPDATE'
    ];

    protected $dates = [
        'TANGGAL_MULAI',
        'TANGGAL_BERAKHIR',
        'TANGGAL_UPDATE',
        'CREATED_AT_FORMATTED',
        'UPDATED_AT_FORMATTED'
        // ✅ FIX: Hapus created_at dan updated_at karena kolom tidak ada
    ];

    protected $casts = [
        'TANGGAL_MULAI' => 'date',
        'TANGGAL_BERAKHIR' => 'date',
        'TANGGAL_UPDATE' => 'date',
        'CREATED_AT_FORMATTED' => 'datetime',
        'UPDATED_AT_FORMATTED' => 'datetime',
        'ID_PENITIPAN' => 'integer',
        'ID_PENITIP' => 'integer',
        'ID_BARANG' => 'integer',
        'ID_BARANGHUNTER' => 'integer',
        'ID_PEGAWAI' => 'integer',
        'CREATED_BY' => 'integer',
        'UPDATED_BY' => 'integer',
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'ID_PENITIP', 'ID_PENITIP');
    }

    public function barang()
    {
        return $this->hasOne(Barang::class, 'ID_PENITIPAN', 'ID_PENITIPAN');
    }

    public function barangHunter()
    {
        return $this->belongsTo(BarangHunter::class, 'ID_BARANGHUNTER', 'ID_BARANGHUNTER');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function createdByPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'CREATED_BY', 'ID_PEGAWAI');
    }

    public function updatedByPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'UPDATED_BY', 'ID_PEGAWAI');
    }

    public function calculateEndDate()
    {
        if ($this->TANGGAL_MULAI) {
            return Carbon::parse($this->TANGGAL_MULAI)->addDays(30);
        }
        return null;
    }

    public function isExpiringSoon($days = 7)
    {
        if ($this->TANGGAL_BERAKHIR) {
            $daysInt = (int)$days;
            return Carbon::now()->addDays($daysInt)->gte(Carbon::parse($this->TANGGAL_BERAKHIR))
                    && Carbon::now()->lte(Carbon::parse($this->TANGGAL_BERAKHIR));
        }
        return false;
    }

    public function scopeAktif($query)
    {
        return $query->where('STATUS_PENITIPAN', 'Aktif');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        $daysInt = (int)$days;
        return $query->where('STATUS_PENITIPAN', 'Aktif')
                    ->whereDate('TANGGAL_BERAKHIR', '<=', Carbon::now()->addDays($daysInt));
    }

    public function markAsCompleted($tanggalBerakhir = null, $userId = null, $userName = null)
    {
        $this->STATUS_PENITIPAN = 'Selesai';
        
        // Tanggal berakhir adalah tanggal keluar
        if ($tanggalBerakhir) {
            $this->TANGGAL_BERAKHIR = $tanggalBerakhir;
        }
        
        // Update audit tracking
        $this->UPDATED_BY = $userId;
        $this->UPDATED_BY_NAME = $userName;
        $this->UPDATED_AT_FORMATTED = now();
        $this->TANGGAL_UPDATE = now()->format('Y-m-d');
        
        return $this->save();
    }

    public function getTanggalKeluarAttribute()
    {
        return $this->TANGGAL_BERAKHIR;
    }

    public function getTanggalMulaiFormattedAttribute()
    {
        return $this->TANGGAL_MULAI ? Carbon::parse($this->TANGGAL_MULAI)->format('d/m/Y') : null;
    }

    public function getTanggalBerakhirFormattedAttribute()
    {
        return $this->TANGGAL_BERAKHIR ? Carbon::parse($this->TANGGAL_BERAKHIR)->format('d/m/Y') : null;
    }

    public function getSisaHariAttribute()
    {
        if ($this->TANGGAL_BERAKHIR && $this->STATUS_PENITIPAN == 'Aktif') {
            $endDate = Carbon::parse($this->TANGGAL_BERAKHIR);
            $now = Carbon::now();
            
            if ($endDate->isFuture()) {
                return $now->diffInDays($endDate);
            }
        }
        return 0;
    }

    public function getDurasiPenitipanAttribute()
    {
        if ($this->TANGGAL_MULAI && $this->TANGGAL_BERAKHIR) {
            $startDate = Carbon::parse($this->TANGGAL_MULAI);
            $endDate = Carbon::parse($this->TANGGAL_BERAKHIR);
            return $startDate->diffInDays($endDate);
        }
        return 0;
    }

    public function setCreateAudit($userId, $userName)
    {
        $this->CREATED_BY = $userId;
        $this->CREATED_BY_NAME = $userName;
        $this->CREATED_AT_FORMATTED = now();
        $this->TANGGAL_UPDATE = now()->format('Y-m-d');
    }

    public function setUpdateAudit($userId, $userName)
    {
        $this->UPDATED_BY = $userId;
        $this->UPDATED_BY_NAME = $userName;
        $this->UPDATED_AT_FORMATTED = now();
        $this->TANGGAL_UPDATE = now()->format('Y-m-d');
    }

     protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set audit tracking saat create
            if (auth()->guard('pegawai')->check()) {
                $user = auth()->guard('pegawai')->user();
                $model->setCreateAudit($user->ID_PEGAWAI, $user->NAMA_PEGAWAI);
            }
        });

        static::updating(function ($model) {
            // Set audit tracking saat update
            if (auth()->guard('pegawai')->check()) {
                $user = auth()->guard('pegawai')->user();
                $model->setUpdateAudit($user->ID_PEGAWAI, $user->NAMA_PEGAWAI);
            }
        });

        // ✅ FIX: Event untuk memastikan data tersimpan (tanpa timestamps Laravel)
        static::saved(function ($model) {
            \Log::info('Penitipan model saved:', [
                'ID_PENITIPAN' => $model->ID_PENITIPAN,
                'STATUS_PENITIPAN' => $model->STATUS_PENITIPAN,
                'UPDATED_BY' => $model->UPDATED_BY,
                'UPDATED_BY_NAME' => $model->UPDATED_BY_NAME,
                'CREATED_AT_FORMATTED' => $model->CREATED_AT_FORMATTED,
                'UPDATED_AT_FORMATTED' => $model->UPDATED_AT_FORMATTED,
                'TANGGAL_UPDATE' => $model->TANGGAL_UPDATE
            ]);
        });
    }

    // Tambahan pencarian global
    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        return $query->where(function ($q) use ($term) {
            $q->where('ID_PENITIP', 'like', $term)
              ->orWhere('ID_BARANG', 'like', $term)
              ->orWhere('STATUS_PENITIPAN', 'like', $term);
        });
    }

    // Tambahan fungsi perpanjang
    public function perpanjang30Hari()
    {
        $this->TANGGAL_BERAKHIR = Carbon::parse($this->TANGGAL_BERAKHIR)->addDays(30);
        $this->STATUS_PENITIPAN = 'Aktif';
        $this->save();
    }

    public function pembeli()
    {
        return $this->barang ? $this->barang->pembeli() : null;
    }

    public function getNamaPenitipUniversalAttribute()
    {
        if ($this->ID_PENITIP && $this->penitip) {
            return $this->penitip->NAMA_PENITIP;
        }

        if ($this->ID_BARANGHUNTER && $this->barangHunter && $this->barangHunter->pegawai && $this->barangHunter->pegawai->ID_ROLE == 6) {
            return $this->barangHunter->pegawai->NAMA_PEGAWAI; // atau NAMA_HUNTER, sesuaikan nama kolom
        }

        return '-';
    }

 /**
     * Mark penitipan as donated and update end date to today
     */
    public function markAsDonated()
    {
        try {
            $this->STATUS_PENITIPAN = 'Donasi';
            $this->TANGGAL_BERAKHIR = Carbon::now()->format('Y-m-d');

            if ($userId && $userName) {
                $this->setUpdateAudit($userId, $userName);
            }
            $this->save();
            
            \Log::info("Penitipan ID {$this->ID_PENITIPAN} marked as donated with end date " . Carbon::now()->format('Y-m-d'));
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Error marking penitipan {$this->ID_PENITIPAN} as donated: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if penitipan is active
     */
    public function isActive()
    {
        return $this->STATUS_PENITIPAN === 'Aktif';
    }

    /**
     * Check if penitipan is completed
     */
    public function isCompleted()
    {
        return $this->STATUS_PENITIPAN === 'Selesai';
    }

    /**
     * Check if penitipan is donated
     */
    public function isDonated()
    {
        return $this->STATUS_PENITIPAN === 'Donasi';
    }

    /**
     * Get days remaining until end date
     */
    public function getDaysRemaining()
    {
        if (!$this->TANGGAL_BERAKHIR) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->TANGGAL_BERAKHIR, false);
    }

    public function isExpired()
    {
        if ($this->TANGGAL_BERAKHIR) {
            return Carbon::now()->gt(Carbon::parse($this->TANGGAL_BERAKHIR));
        }
        return false;
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass()
    {
        switch ($this->STATUS_PENITIPAN) {
            case 'Aktif':
                return 'bg-success';
            case 'Selesai':
                return 'bg-primary';
            case 'Donasi':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Scope for active penitipan
     */
    public function scopeActive($query)
    {
        return $query->where('STATUS_PENITIPAN', 'Aktif');
    }

    /**
     * Scope for completed penitipan
     */
    public function scopeCompleted($query)
    {
        return $query->where('STATUS_PENITIPAN', 'Selesai');
    }

    /**
     * Scope for donated penitipan
     */
    public function scopeDonated($query)
    {
        return $query->where('STATUS_PENITIPAN', 'Donasi');
    }

    /**
     * Scope for expired penitipan
     */
    public function scopeExpired($query)
    {
        return $query->where('STATUS_PENITIPAN', 'Aktif')
                    ->where('TANGGAL_BERAKHIR', '<', Carbon::now());
    }

    /**
     * Get formatted start date
     */
    public function getFormattedStartDate()
    {
        return $this->TANGGAL_MULAI ? Carbon::parse($this->TANGGAL_MULAI)->format('d F Y') : '-';
    }

    /**
     * Get formatted end date
     */
    public function getFormattedEndDate()
    {
        return $this->TANGGAL_BERAKHIR ? Carbon::parse($this->TANGGAL_BERAKHIR)->format('d F Y') : '-';
    }

    /**
     * Get status message for UI
     */
    public function getStatusMessage()
    {
        $daysRemaining = $this->getDaysRemaining();
        
        switch ($this->STATUS_PENITIPAN) {
            case 'Aktif':
                if ($daysRemaining === null) {
                    return 'Penitipan aktif';
                } elseif ($daysRemaining > 7) {
                    return "Penitipan aktif, {$daysRemaining} hari tersisa";
                } elseif ($daysRemaining > 0) {
                    return "Akan berakhir dalam {$daysRemaining} hari";
                } elseif ($daysRemaining == 0) {
                    return 'Berakhir hari ini';
                } else {
                    return 'Telah berakhir ' . abs($daysRemaining) . ' hari yang lalu';
                }
            case 'Selesai':
                return 'Barang telah terjual';
            case 'Donasi':
                return 'Barang telah didonasikan';
            default:
                return $this->STATUS_PENITIPAN;
        }
    }

    /**
     * Get alert class for status message
     */
    public function getStatusAlertClass()
    {
        $daysRemaining = $this->getDaysRemaining();
        
        switch ($this->STATUS_PENITIPAN) {
            case 'Aktif':
                if ($daysRemaining === null || $daysRemaining > 7) {
                    return 'alert-success';
                } elseif ($daysRemaining > 0) {
                    return 'alert-warning';
                } else {
                    return 'alert-danger';
                }
            case 'Selesai':
                return 'alert-primary';
            case 'Donasi':
                return 'alert-info';
            default:
                return 'alert-secondary';
        }
    }

    /**
     * Check if penitipan can be extended
     */
    public function canBeExtended()
    {
        if ($this->STATUS_PENITIPAN !== 'Aktif') {
            return false;
        }
        
        $daysRemaining = $this->getDaysRemaining();
        return $daysRemaining !== null && $daysRemaining <= 7 && $daysRemaining >= 0;
    }

    /**
     * Check if item can be picked up
     */
    public function canBePickedUp()
    {
        if ($this->STATUS_PENITIPAN !== 'Aktif') {
            return false;
        }
        
        $daysRemaining = $this->getDaysRemaining();
        return $daysRemaining !== null && $daysRemaining <= 0;
    }

    /**
     * Get duration in days
     */
    public function getDurationInDays()
    {
        if (!$this->TANGGAL_MULAI || !$this->TANGGAL_BERAKHIR) {
            return null;
        }
        
        return Carbon::parse($this->TANGGAL_MULAI)->diffInDays($this->TANGGAL_BERAKHIR);
    }

    /**
     * Extend penitipan by specified days (default 30 days)
     */
    public function extend($days = 30)
    {
        try {
            if (!$this->canBeExtended()) {
                throw new \Exception('Penitipan cannot be extended');
            }
            
            $currentEndDate = Carbon::parse($this->TANGGAL_BERAKHIR);
            $newEndDate = $currentEndDate->addDays($days);
            
            $this->TANGGAL_BERAKHIR = $newEndDate->format('Y-m-d');
            $this->save();
            
            \Log::info("Penitipan ID {$this->ID_PENITIPAN} extended by {$days} days. New end date: " . $newEndDate->format('Y-m-d'));
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Error extending penitipan {$this->ID_PENITIPAN}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get earnings calculation for completed penitipan
     */
    public function getEarnings()
    {
        if ($this->STATUS_PENITIPAN !== 'Selesai') {
            return 0;
        }

        // Find the transaction related to this penitipan
        $transaction = \App\Models\Transaksi::whereHas('barang', function($query) {
            $query->where('ID_PENITIPAN', $this->ID_PENITIPAN);
        })->where('STATUS_TRANSAKSI', 'Selesai')->first();

        if (!$transaction) {
            return 0;
        }

        // Calculate earnings (80% of sale price)
        $salePrice = $transaction->TOTAL_TRANSAKSI;
        $commission = $salePrice * 0.2; // 20% commission
        return $salePrice - $commission;
    }

    /**
     * Get related transaction
     */
    public function getTransaction()
    {
        return \App\Models\Transaksi::whereHas('barang', function($query) {
            $query->where('ID_PENITIPAN', $this->ID_PENITIPAN);
        })->where('STATUS_TRANSAKSI', 'Selesai')->first();
    }

    /**
     * Generate item code for display
     */
    public function getItemCode()
    {
        if (!$this->barang) {
            return 'N/A';
        }
        
        $prefix = substr($this->barang->NAMA_BARANG, 0, 1);
        return sprintf("%s%03d", strtoupper($prefix), $this->ID_PENITIPAN);
    }

    /**
     * Get formatted earnings for display
     */
    public function getFormattedEarnings()
    {
        $earnings = $this->getEarnings();
        return 'Rp ' . number_format($earnings, 0, ',', '.');
    }

    /**
     * Check if consignment period is about to expire (within 7 days)
     */
    public function isAboutToExpire()
    {
        if ($this->STATUS_PENITIPAN !== 'Aktif') {
            return false;
        }
        
        $daysRemaining = $this->getDaysRemaining();
        return $daysRemaining !== null && $daysRemaining <= 7 && $daysRemaining > 0;
    }

    /**
     * Get remaining time in a human readable format
     */
    public function getRemainingTimeText()
    {
        if (!$this->TANGGAL_BERAKHIR) {
            return 'Tidak ada batas waktu';
        }
        
        $daysRemaining = $this->getDaysRemaining();
        
        if ($daysRemaining === null) {
            return 'Tidak ada batas waktu';
        } elseif ($daysRemaining > 0) {
            return $daysRemaining . ' hari tersisa';
        } elseif ($daysRemaining == 0) {
            return 'Berakhir hari ini';
        } else {
            return 'Berakhir ' . abs($daysRemaining) . ' hari yang lalu';
        }
    }

    /**
     * Get progress percentage (0-100) based on duration
     */
    public function getProgressPercentage()
    {
        if (!$this->TANGGAL_MULAI || !$this->TANGGAL_BERAKHIR) {
            return 0;
        }
        
        $startDate = Carbon::parse($this->TANGGAL_MULAI);
        $endDate = Carbon::parse($this->TANGGAL_BERAKHIR);
        $today = Carbon::now();
        
        $totalDays = $startDate->diffInDays($endDate);
        $elapsedDays = $startDate->diffInDays($today);
        
        if ($totalDays <= 0) {
            return 100;
        }
        
        $percentage = min(100, max(0, ($elapsedDays / $totalDays) * 100));
        
        return round($percentage, 1);
    }

    /**
     * Get CSS class for progress bar based on remaining time
     */
    public function getProgressBarClass()
    {
        $percentage = $this->getProgressPercentage();
        
        if ($percentage >= 90) {
            return 'bg-danger';
        } elseif ($percentage >= 75) {
            return 'bg-warning';
        } else {
            return 'bg-success';
        }
    }

    /**
     * Static method to get all penitipan that need attention
     */
    public static function getNeedingAttention()
    {
        return self::where('STATUS_PENITIPAN', 'Aktif')
                  ->where('TANGGAL_BERAKHIR', '<=', Carbon::now()->addDays(7))
                  ->orderBy('TANGGAL_BERAKHIR', 'asc')
                  ->get();
    }

    /**
     * Static method to get expired penitipan that can be auto-donated
     */
    public static function getExpiredForDonation($graceDays = 7)
    {
        $cutoffDate = Carbon::now()->subDays($graceDays);
        
        return self::where('STATUS_PENITIPAN', 'Aktif')
                  ->where('TANGGAL_BERAKHIR', '<', $cutoffDate)
                  ->with(['penitip', 'barang'])
                  ->get();
    }

    /**
     * Static method to bulk update completed penitipan
     */
    public static function updateCompletedFromTransactions()
    {
        $updatedCount = 0;
        
        // Find all transactions that are completed but penitipan is still active
        $completedTransactions = \App\Models\Transaksi::where('STATUS_TRANSAKSI', 'Selesai')
                                                      ->where('STATUS_VERIFIKASI', 'Valid')
                                                      ->whereHas('penitipan', function($query) {
                                                          $query->where('STATUS_PENITIPAN', 'Aktif');
                                                      })
                                                      ->with('penitipan')
                                                      ->get();
        
        foreach ($completedTransactions as $transaction) {
            if ($transaction->penitipan && $transaction->penitipan->isActive()) {
                if ($transaction->penitipan->markAsCompleted()) {
                    $updatedCount++;
                }
            }
        }
        
        return $updatedCount;
    }

    /**
     * Static method to auto-donate expired penitipan after grace period
     */
    public static function autoDonateExpired($graceDays = 7)
    {
        $expiredPenitipan = self::getExpiredForDonation($graceDays);
        $donatedCount = 0;
        
        foreach ($expiredPenitipan as $penitipan) {
            if ($penitipan->markAsDonated()) {
                $donatedCount++;
                
                // Also update the related barang status
                if ($penitipan->barang) {
                    $penitipan->barang->STATUS = 'Donasi';
                    $penitipan->barang->save();
                }
            }
        }
        
        return $donatedCount;
    }

    /**
     * Get the hunter assigned to this consignment
     */
    public function getHunterName()
    {
        if ($this->barangHunter && $this->barangHunter->pegawai) {
            return $this->barangHunter->pegawai->NAMA_PEGAWAI;
        }
        
        return 'Tidak ada hunter';
    }

    /**
     * Check if this penitipan has any related transactions
     */
    public function hasTransactions()
    {
        return \App\Models\Transaksi::whereHas('barang', function($query) {
            $query->where('ID_PENITIPAN', $this->ID_PENITIPAN);
        })->exists();
    }

    /**
     * Get all transactions related to this penitipan
     */
    public function getRelatedTransactions()
    {
        return \App\Models\Transaksi::whereHas('barang', function($query) {
            $query->where('ID_PENITIPAN', $this->ID_PENITIPAN);
        })->with(['pembeli', 'barang'])->get();
    }

    /**
     * Get commission information for this penitipan
     */
    public function getCommissionInfo()
    {
        $transaction = $this->getTransaction();
        
        if (!$transaction) {
            return [
                'sale_price' => 0,
                'commission_rate' => 0.2,
                'commission_amount' => 0,
                'penitip_earnings' => 0,
                'reusemart_earnings' => 0
            ];
        }
        
        $salePrice = $transaction->TOTAL_TRANSAKSI;
        $commissionRate = 0.2; // 20%
        $commissionAmount = $salePrice * $commissionRate;
        $penitipEarnings = $salePrice - $commissionAmount;
        
        return [
            'sale_price' => $salePrice,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'penitip_earnings' => $penitipEarnings,
            'reusemart_earnings' => $commissionAmount
        ];
    }

    /**
     * Convert to array for API responses
     */
    public function toApiArray()
    {
        return [
            'id' => $this->ID_PENITIPAN,
            'penitip_id' => $this->ID_PENITIP,
            'barang_hunter_id' => $this->ID_BARANGHUNTER,
            'start_date' => $this->TANGGAL_MULAI ? $this->TANGGAL_MULAI->format('Y-m-d') : null,
            'end_date' => $this->TANGGAL_BERAKHIR ? $this->TANGGAL_BERAKHIR->format('Y-m-d') : null,
            'status' => $this->STATUS_PENITIPAN,
            'days_remaining' => $this->getDaysRemaining(),
            'is_expired' => $this->isExpired(),
            'is_active' => $this->isActive(),
            'is_completed' => $this->isCompleted(),
            'is_donated' => $this->isDonated(),
            'can_be_extended' => $this->canBeExtended(),
            'can_be_picked_up' => $this->canBePickedUp(),
            'progress_percentage' => $this->getProgressPercentage(),
            'status_message' => $this->getStatusMessage(),
            'item_code' => $this->getItemCode(),
            'earnings' => $this->getEarnings(),
            'formatted_earnings' => $this->getFormattedEarnings(),
            'commission_info' => $this->getCommissionInfo(),
            'barang' => $this->barang ? $this->barang->toArray() : null,
            'penitip' => $this->penitip ? [
                'id' => $this->penitip->ID_PENITIP,
                'name' => $this->penitip->NAMA_PENITIP,
                'email' => $this->penitip->EMAIL_PENITIP,
                'rating' => $this->penitip->RATING_PENITIP
            ] : null,
            'hunter' => $this->barangHunter && $this->barangHunter->pegawai ? [
                'id' => $this->barangHunter->pegawai->ID_PEGAWAI,
                'name' => $this->barangHunter->pegawai->NAMA_PEGAWAI
            ] : null
        ];
    }
}
