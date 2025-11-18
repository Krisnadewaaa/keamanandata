<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeranjangPembeli extends Model
{
    use HasFactory;

    protected $table = 'keranjang_pembeli';
    protected $primaryKey = 'ID_KERANJANG';
    public $timestamps = false;

    protected $fillable = [
        'ID_PEMBELI',
        'ID_BARANG'
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'ID_PEMBELI', 'ID_PEMBELI');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'ID_BARANG', 'ID_BARANG');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'ID_KERANJANG', 'ID_KERANJANG');
    }
    /**
     * Check if cart item has an associated transaction
     */
    public function hasTransaction()
    {
        return $this->transaksi()->exists();
    }

    /**
     * Get all active cart items (not in a transaction)
     */
    public static function getActiveItems($pembeliId)
    {
        return self::where('ID_PEMBELI', $pembeliId)
                    ->whereDoesntHave('transaksi')
                    ->with('barang')
                    ->get();
    }

    /**
     * Calculate subtotal of all active cart items
     */
    public static function calculateSubtotal($pembeliId)
    {
        $items = self::getActiveItems($pembeliId);
        
        return $items->sum(function($item) {
            return $item->barang->HARGA;
        });
    }

    /**
     * Calculate shipping cost based on subtotal
     */
    public static function calculateShippingCost($subtotal)
    {
        // Free shipping for purchases over 1.5 million
        return $subtotal >= 1500000 ? 0 : 100000;
    }

    /**
     * Calculate total with shipping and point discount
     */
    public static function calculateTotal($pembeliId, $pointsDiscount = 0)
    {
        $subtotal = self::calculateSubtotal($pembeliId);
        $shippingCost = self::calculateShippingCost($subtotal);
        
        return $subtotal + $shippingCost - $pointsDiscount;
    }
}