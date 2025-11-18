<?php
// File: app/Models/Transaksi.php (Enhanced)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'ID_TRANSAKSI';
    public $timestamps = false;

    protected $fillable = [
        'ID_PEGAWAI',
        'ID_KOMISI',
        'ID_PEMBELI',
        'ID_BARANG',
        'ID_PENITIPAN',
        'ID_KERANJANG',
        'NOMOR_TRANSAKSI',
        'STATUS_TRANSAKSI',
        'METODE_PEMBAYARAN',
        'TANGGAL_TRANSAKSI',
        'METODE_PENGIRIMAN',
        'STATUS_PENGIRIMAN',
        'BIAYA_PENGIRIMAN',
        'TOTAL_TRANSAKSI',
        'BATAS_WAKTU_PEMBAYARAN',
        'POIN_DITUKAR',
        'BUKTI_PEMBAYARAN',
        'STATUS_VERIFIKASI',
        'TANGGAL_UPLOAD_BUKTI',
        'TANGGAL_VERIFIKASI',
        'VERIFIED_BY',
        'CATATAN_VERIFIKASI',
        'RATING_BARANG',
        'RATING_PENITIP',
    ];

    protected $dates = [
        'TANGGAL_TRANSAKSI',
        'BATAS_WAKTU_PEMBAYARAN',
        'TANGGAL_UPLOAD_BUKTI',
        'TANGGAL_VERIFIKASI'
    ];

    protected $casts = [
        'BATAS_WAKTU_PEMBAYARAN' => 'datetime',
        'TANGGAL_TRANSAKSI' => 'datetime',
        'TANGGAL_UPLOAD_BUKTI' => 'datetime',
        'TANGGAL_VERIFIKASI' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function komisi()
    {
        return $this->belongsTo(Komisi::class, 'ID_KOMISI', 'ID_KOMISI');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function penitipan()
    {
        return $this->belongsTo(Penitipan::class, 'ID_PENITIPAN', 'ID_PENITIPAN');
    }

    public function keranjang()
    {
        return $this->belongsTo(KeranjangPembeli::class, 'ID_KERANJANG', 'ID_KERANJANG');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Pegawai::class, 'VERIFIED_BY', 'ID_PEGAWAI');
    }

    /**
     * Generate transaction number
     * Format: YY.MM.XXX (year.month.sequence)
     */
    public static function generateTransactionNumber()
    {
        $year = date('y');
        $month = date('m');
        
        // Get the last transaction number for this month
        $lastTransaction = self::where('NOMOR_TRANSAKSI', 'like', "{$year}.{$month}.%")
                              ->orderBy('NOMOR_TRANSAKSI', 'desc')
                              ->first();
        
        if ($lastTransaction) {
            // Extract sequence number and increment
            $parts = explode('.', $lastTransaction->NOMOR_TRANSAKSI);
            $sequence = (int)$parts[2] + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf("%s.%s.%03d", $year, $month, $sequence);
    }

    /**
     * Check if payment deadline has passed
     */
    public function isPaymentExpired()
    {
        if (!$this->BATAS_WAKTU_PEMBAYARAN) {
            return false;
        }
        
        return Carbon::now()->gt($this->BATAS_WAKTU_PEMBAYARAN);
    }

    /**
     * Get remaining payment time in minutes
     */
    public function getRemainingPaymentTime()
    {
        if (!$this->BATAS_WAKTU_PEMBAYARAN) {
            return 0;
        }
        
        $now = Carbon::now();
        $deadline = $this->BATAS_WAKTU_PEMBAYARAN;
        
        if ($now->gt($deadline)) {
            return 0;
        }
        
        return $now->diffInMinutes($deadline);
    }

    /**
     * Get remaining payment time in seconds
     */
    public function getRemainingPaymentTimeInSeconds()
    {
        if (!$this->BATAS_WAKTU_PEMBAYARAN) {
            return 0;
        }
        
        $now = Carbon::now();
        $deadline = $this->BATAS_WAKTU_PEMBAYARAN;
        
        if ($now->gt($deadline)) {
            return 0;
        }
        
        return $now->diffInSeconds($deadline);
    }

    /**
     * Cancel expired transactions and restore points/stock
     */
    public static function cancelExpiredTransactions()
    {
        $expiredTransactions = self::where('STATUS_TRANSAKSI', 'Menunggu Pembayaran')
                                 ->where('BATAS_WAKTU_PEMBAYARAN', '<', Carbon::now())
                                 ->get();

        $cancelledCount = 0;

        foreach ($expiredTransactions as $transaction) {
            try {
                \DB::beginTransaction();

                // Return points to buyer if they were redeemed
                if ($transaction->POIN_DITUKAR > 0) {
                    $pembeli = $transaction->pembeli;
                    if ($pembeli) {
                        $pembeli->POINT_PEMBELI += $transaction->POIN_DITUKAR;
                        $pembeli->save();
                        
                        \Log::info("Returned {$transaction->POIN_DITUKAR} points to buyer {$pembeli->ID_PEMBELI}");
                    }
                }

                // Return stock to product
                $barang = $transaction->barang;
                if ($barang) {
                    $barang->stok += 1;
                    // Only change status to Tersedia if stock > 0
                    if ($barang->stok > 0) {
                        $barang->STATUS = 'Tersedia';
                    }
                    $barang->save();
                    
                    \Log::info("Restored stock for product {$barang->ID_BARANG}, new stock: {$barang->stok}");
                }

                // Update transaction status
                $transaction->STATUS_TRANSAKSI = 'Batal';
                $transaction->STATUS_VERIFIKASI = 'Invalid';
                $transaction->CATATAN_VERIFIKASI = 'Transaksi dibatalkan otomatis karena melewati batas waktu pembayaran 1 menit.';
                $transaction->save();

                \DB::commit();
                $cancelledCount++;
                
                \Log::info("Transaction {$transaction->ID_TRANSAKSI} cancelled due to payment timeout");
                
            } catch (\Exception $e) {
                \DB::rollback();
                \Log::error("Failed to cancel transaction {$transaction->ID_TRANSAKSI}: " . $e->getMessage());
            }
        }

        return $cancelledCount;
    }

    /**
     * Cancel specific transaction by ID
     */
    public static function cancelTransaction($transactionId)
    {
        $transaction = self::find($transactionId);
        
        if (!$transaction || $transaction->STATUS_TRANSAKSI !== 'Menunggu Pembayaran') {
            return false;
        }

        try {
            \DB::beginTransaction();

            // Return points to buyer if they were redeemed
            if ($transaction->POIN_DITUKAR > 0) {
                $pembeli = $transaction->pembeli;
                if ($pembeli) {
                    $pembeli->POINT_PEMBELI += $transaction->POIN_DITUKAR;
                    $pembeli->save();
                }
            }

            // Return stock to product
            $barang = $transaction->barang;
            if ($barang) {
                $barang->stok += 1;
                if ($barang->stok > 0) {
                    $barang->STATUS = 'Tersedia';
                }
                $barang->save();
            }

            // Update transaction status
            $transaction->STATUS_TRANSAKSI = 'Batal';
            $transaction->STATUS_VERIFIKASI = 'Invalid';
            $transaction->CATATAN_VERIFIKASI = 'Transaksi dibatalkan karena melewati batas waktu pembayaran.';
            $transaction->save();

            \DB::commit();
            return true;
            
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error("Failed to cancel transaction {$transactionId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process successful payment
     */
    public function processSuccessfulPayment()
    {
        try {
            \DB::beginTransaction();

            // Update transaction status
            $this->STATUS_TRANSAKSI = 'Disiapkan';
            $this->STATUS_VERIFIKASI = 'Valid';
            $this->TANGGAL_VERIFIKASI = Carbon::now();
            $this->save();

            // Calculate and add points to buyer
            $subtotal = $this->TOTAL_TRANSAKSI - ($this->BIAYA_PENGIRIMAN ?? 0) + ($this->POIN_DITUKAR * 1000);
            $pointsEarned = floor($subtotal / 10000);
            
            // Add 20% bonus if purchase > 500,000
            if ($subtotal > 500000) {
                $pointsEarned = ceil($pointsEarned * 1.2);
            }

            $pembeli = $this->pembeli;
            if ($pembeli && $pointsEarned > 0) {
                $pembeli->POINT_PEMBELI += $pointsEarned;
                $pembeli->save();
                
                \Log::info("Added {$pointsEarned} points to buyer {$pembeli->ID_PEMBELI} for transaction {$this->ID_TRANSAKSI}");
            }

            // Update commission status
            $komisi = $this->komisi;
            if ($komisi) {
                $komisi->STATUS_KOMISI = 'Dibayar';
                $komisi->save();
            }

            \DB::commit();
            return true;
            
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error("Failed to process successful payment for transaction {$this->ID_TRANSAKSI}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update status penitipan terkait transaksi menjadi "Selesai" dan set tanggal berakhir hari ini
     */
    public function updateRelatedPenitipanStatusAndDate()
    {
        try {
            if ($this->ID_PENITIPAN) {
                $penitipan = Penitipan::find($this->ID_PENITIPAN);
                
                if ($penitipan && $penitipan->STATUS_PENITIPAN === 'Aktif') {
                    $penitipan->STATUS_PENITIPAN = 'Selesai';
                    
                    // PENTING: Set tanggal berakhir menjadi hari ini
                    $penitipan->TANGGAL_BERAKHIR = Carbon::now()->format('Y-m-d');
                    
                    $penitipan->save();
                    
                    \Log::info("Status penitipan ID {$penitipan->ID_PENITIPAN} berhasil diubah menjadi 'Selesai' dengan tanggal berakhir " . Carbon::now()->format('Y-m-d') . " untuk transaksi ID {$this->ID_TRANSAKSI}");
                    
                    return true;
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            \Log::error("Error updating penitipan status and date for transaction {$this->ID_TRANSAKSI}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if this transaction is for a consigned item
     */
    public function isConsignedItem()
    {
        return !is_null($this->ID_PENITIPAN) && $this->penitipan;
    }

    /**
     * Get penitip info if this is a consigned item
     */
    public function getPenitipInfo()
    {
        if ($this->isConsignedItem() && $this->penitipan->penitip) {
            return $this->penitipan->penitip;
        }
        
        return null;
    }

    /**
     * Format remaining time as MM:SS
     */
    public function getFormattedRemainingTime()
    {
        $seconds = $this->getRemainingPaymentTimeInSeconds();
        
        if ($seconds <= 0) {
            return '00:00';
        }
        
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    /**
     * Check if transaction can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->STATUS_TRANSAKSI, ['Menunggu Pembayaran', 'Menunggu Konfirmasi']);
    }

    /**
     * Check if transaction can be paid
     */
    public function canBePaid()
    {
        return $this->STATUS_TRANSAKSI === 'Menunggu Pembayaran' && !$this->isPaymentExpired();
    }

    /**
     * Scope for pending payment transactions
     */
    public function scopePendingPayment($query)
    {
        return $query->where('STATUS_TRANSAKSI', 'Menunggu Pembayaran');
    }

    /**
     * Scope for expired transactions
     */
    public function scopeExpired($query)
    {
        return $query->where('STATUS_TRANSAKSI', 'Menunggu Pembayaran')
                    ->where('BATAS_WAKTU_PEMBAYARAN', '<', Carbon::now());
    }

    /**
     * Scope for awaiting verification
     */
    public function scopeAwaitingVerification($query)
    {
        return $query->where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')
                    ->whereNotNull('BUKTI_PEMBAYARAN');
    }

    public function alamat()
    {
        return $this->hasOneThrough(
            AlamatPembeli::class,
            Pembeli::class,
            'ID_PEMBELI', // foreign key Pembeli di tabel Alamat
            'ID_PEMBELI', // foreign key Pembeli di tabel Transaksi
            'ID_PEMBELI', // local key di Transaksi
            'ID_PEMBELI'  // local key di Pembeli
        )->where('IS_DEFAULT', true);
    }

    // Model Transaksi.php

    public function kurir()
    {
        return $this->pegawai()->whereHas('role', function($q) {
            $q->where('ID_ROLE', 5);
        });
    }

    public function getKurirAttribute()
    {
        return $this->kurir()->first(); // ambil satu kurir terkait
    }

    /**
     * Calculate net price after commission
     */
    public function getNetPrice()
    {
        $totalKomisi = $this->komisi ? $this->komisi->TOTAL_KOMISI : 0;
        return $this->TOTAL_TRANSAKSI - $totalKomisi;
    }

    /**
     * Calculate fast sale bonus
     */
    public function getFastSaleBonus()
    {
        if (!$this->barang || !$this->barang->penitipan) {
            return 0;
        }
        
        $tanggalMasuk = \Carbon\Carbon::parse($this->barang->penitipan->TANGGAL_MULAI);
        $tanggalLaku = \Carbon\Carbon::parse($this->TANGGAL_TRANSAKSI);
        $selisihHari = $tanggalMasuk->diffInDays($tanggalLaku);
        
        if ($selisihHari <= 7) {
            $totalKomisi = $this->komisi ? $this->komisi->TOTAL_KOMISI : 0;
            $komisiReusemart = $totalKomisi * 0.85; // 85% dari total komisi untuk ReUseMart
            return $komisiReusemart * 0.10; // 10% dari komisi ReUseMart sebagai bonus
        }
        
        return 0;
    }

    /**
     * Get penitip earnings (net price + bonus)
     */
    public function getPenitipEarnings()
    {
        return $this->getNetPrice() + $this->getFastSaleBonus();
    }

    /**
     * Generate product code for reports
     */
    public function getProductCode()
    {
        if (!$this->barang) {
            return '-';
        }
        
        $firstLetter = strtoupper(substr($this->barang->NAMA_BARANG, 0, 1));
        return $firstLetter . $this->barang->ID_BARANG;
    }

    // Add these methods to your Komisi model (App\Models\Komisi.php)

    /**
     * Calculate commission breakdown
     */
    public function getCommissionBreakdown()
    {
        $total = $this->TOTAL_KOMISI ?? 0;
        
        return [
            'total' => $total,
            'reusemart' => $total * 0.85, // 85% untuk ReUseMart
            'hunter' => $total * 0.15,    // 15% untuk Hunter (jika ada)
            'penitip_bonus_eligible' => $total * 0.085 // 8.5% maksimal untuk bonus penitip (10% dari 85%)
        ];
    }

    /**
     * Check if transaction qualifies for fast sale bonus
     */
    public function qualifiesForFastSaleBonus()
    {
        if (!$this->transaksi || !$this->transaksi->barang || !$this->transaksi->barang->penitipan) {
            return false;
        }
        
        $tanggalMasuk = \Carbon\Carbon::parse($this->transaksi->barang->penitipan->TANGGAL_MULAI);
        $tanggalLaku = \Carbon\Carbon::parse($this->transaksi->TANGGAL_TRANSAKSI);
        
        return $tanggalMasuk->diffInDays($tanggalLaku) <= 7;
    }
}