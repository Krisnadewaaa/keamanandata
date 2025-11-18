<?php
// File: app/Http/Controllers/Pembeli/CartController.php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KeranjangPembeli;
use App\Models\Barang;
use App\Models\AlamatPembeli;
use App\Models\Transaksi;
use App\Models\Komisi;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Pegawai;
use App\Models\Reusemart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display cart items
     */
    public function index()
    {
        $pembeli = Auth::guard('pembeli')->user();
        $cartItems = KeranjangPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                    ->whereDoesntHave('transaksi')
                                    ->with('barang')
                                    ->get();
        
        $subtotal = $cartItems->sum(function($item) {
            return $item->barang->HARGA;
        });
        
        $shippingCost = $subtotal >= 1500000 ? 0 : 100000;
        $total = $subtotal + $shippingCost;
        
        return view('pembeli.cart.index', compact('cartItems', 'subtotal', 'shippingCost', 'total', 'pembeli'));
    }
    
    /**
     * Add item to cart
     */
    public function addToCart(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);
        
        // Check if product is available
        if ($barang->STATUS != 'Tersedia' || $barang->stok < 1) {
            return redirect()->back()->with('error', 'Barang tidak tersedia atau stok habis.');
        }
        
        $pembeli = Auth::guard('pembeli')->user();
        
        // Check if item already in cart
        $existingItem = KeranjangPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                        ->where('ID_BARANG', $id)
                                        ->whereDoesntHave('transaksi')
                                        ->first();
        
        if ($existingItem) {
            return redirect()->back()->with('error', 'Barang sudah ada di keranjang Anda.');
        }
        
        // Add to cart
        KeranjangPembeli::create([
            'ID_PEMBELI' => $pembeli->ID_PEMBELI,
            'ID_BARANG' => $id
        ]);
        
        return redirect()->back()->with('success', 'Barang berhasil ditambahkan ke keranjang.');
    }
    
    /**
     * Remove item from cart
     */
    public function removeFromCart($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        
        $cartItem = KeranjangPembeli::where('ID_KERANJANG', $id)
                                    ->where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                    ->whereDoesntHave('transaksi')
                                    ->firstOrFail();
        
        $cartItem->delete();
        
        return redirect()->route('pembeli.cart.index')->with('success', 'Barang berhasil dihapus dari keranjang.');
    }
    
    /**
     * Show checkout page
     */
    public function checkout()
    {
        $pembeli = Auth::guard('pembeli')->user();
        $cartItems = KeranjangPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                    ->whereDoesntHave('transaksi')
                                    ->with('barang')
                                    ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('pembeli.cart.index')->with('error', 'Keranjang Anda kosong.');
        }
        
        $alamat = AlamatPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)->get();
        $defaultAlamat = AlamatPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                     ->where('IS_DEFAULT', true)
                                     ->first();
        
        $subtotal = $cartItems->sum(function($item) {
            return $item->barang->HARGA;
        });
        
        $shippingCost = $subtotal >= 1500000 ? 0 : 100000;
        $total = $subtotal + $shippingCost;
        
        // Calculate potential points earned
        $pointsEarned = floor($subtotal / 10000);
        // Add 20% bonus if purchase > 500,000
        if ($subtotal > 500000) {
            $pointsEarned = ceil($pointsEarned * 1.2);
        }
        
        return view('pembeli.cart.checkout', compact(
            'cartItems', 
            'alamat', 
            'defaultAlamat', 
            'subtotal', 
            'shippingCost', 
            'total', 
            'pembeli',
            'pointsEarned'
        ));
    }
    
    /**
     * Process order
     */
    public function processOrder(Request $request)
    {
        $request->validate([
            'shipping_method' => 'required|in:kurir,ambil_sendiri',
            'payment_method' => 'required|in:transfer',
            'redeemed_points' => 'nullable|integer|min:0',
        ]);
        
        if ($request->shipping_method == 'kurir') {
            $request->validate(['alamat_id' => 'required|exists:alamat,ID_ALAMAT']);
        }
        
        $pembeli = Auth::guard('pembeli')->user();
        $cartItems = KeranjangPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                    ->whereDoesntHave('transaksi')
                                    ->with('barang')
                                    ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('pembeli.cart.index')->with('error', 'Keranjang Anda kosong.');
        }
        
        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->barang->HARGA;
        });
        
        $shippingCost = $request->shipping_method == 'kurir' ? ($subtotal >= 1500000 ? 0 : 100000) : 0;
        $pointDiscount = 0;
        
        // Process point redemption
        $redeemedPoints = $request->redeemed_points ?? 0;
        if ($redeemedPoints > 0) {
            if ($redeemedPoints > $pembeli->POINT_PEMBELI) {
                return redirect()->back()->with('error', 'Poin yang ditukar melebihi poin yang Anda miliki.');
            }
            
            // Each point is worth Rp 1,000
            $pointDiscount = $redeemedPoints * 10000;
            
            // Update user points
            $pembeli->POINT_PEMBELI -= $redeemedPoints;
            $pembeli->save();
        }
        
        $total = $subtotal + $shippingCost - $pointDiscount;
        
        // Create transaction for each item
        DB::beginTransaction();
        
        try {
            foreach ($cartItems as $cartItem) {
                // Check if stock is still available
                $barang = Barang::find($cartItem->ID_BARANG);
                if ($barang->STATUS != 'Tersedia' || $barang->stok < 1) {
                    throw new \Exception("Barang {$barang->NAMA_BARANG} tidak tersedia atau stok habis.");
                }
                
                // Generate transaction number
                $nomorTransaksi = Transaksi::generateTransactionNumber();
                
                // // Create komisi record
                // $komisi = new Komisi();
                // $komisi->TOTAL_KOMISI = $barang->HARGA * 0.2; // 20% commission
                // $komisi->TANGGAL_KOMISI = Carbon::now();
                // $komisi->STATUS_KOMISI = 'Menunggu';
                // $komisi->KOMISI_REUSEMART = $komisi->TOTAL_KOMISI * 0.8; // 80% for ReUseMart
                $komisi = new Komisi();
                $komisi->save();
                
                // Create transaction
                $transaksi = new Transaksi();
                $transaksi->ID_PEGAWAI = 5; // Default CS staff
                $transaksi->ID_KOMISI = $komisi->ID_KOMISI;
                $transaksi->ID_PEMBELI = $pembeli->ID_PEMBELI;
                $transaksi->ID_BARANG = $barang->ID_BARANG;
                $transaksi->ID_PENITIPAN = $barang->ID_PENITIPAN;
                $transaksi->ID_KERANJANG = $cartItem->ID_KERANJANG;
                $transaksi->NOMOR_TRANSAKSI = $nomorTransaksi;
                $transaksi->STATUS_TRANSAKSI = 'Menunggu Pembayaran';
                $transaksi->METODE_PEMBAYARAN = $request->payment_method;
                $transaksi->TANGGAL_TRANSAKSI = Carbon::now();
                $transaksi->METODE_PENGIRIMAN = $request->shipping_method == 'kurir' ? 'Kurir' : 'Ambil Sendiri';
                $transaksi->STATUS_PENGIRIMAN = $request->shipping_method == 'kurir' ? 'Menunggu' : 'Belum Diambil';
                $transaksi->BIAYA_PENGIRIMAN = $shippingCost;
                $transaksi->TOTAL_TRANSAKSI = $barang->HARGA + $shippingCost - ($pointDiscount / $cartItems->count());
                $transaksi->BATAS_WAKTU_PEMBAYARAN = Carbon::now()->addMinutes(1); // 1 minute deadline
                $transaksi->POIN_DITUKAR = $redeemedPoints / $cartItems->count(); // Distribute points across items
                $transaksi->save();
                
                // // Update komisi with transaction ID
                // $komisi->ID_TRANSAKSI = $transaksi->ID_TRANSAKSI;
                // $komisi->save();
                
                // Update stock and mark as sold out
                $barang->stok -= 1;
                $barang->STATUS = 'Sold Out';
                $barang->ID_PEMBELI = $pembeli->ID_PEMBELI;
                $barang->ID_TRANSAKSI = $transaksi->ID_TRANSAKSI;
                $barang->save();
            }
            
            // Calculate points earned
            $pointsEarned = floor($subtotal / 10000);
            // Add 20% bonus if purchase > 500,000
            if ($subtotal > 500000) {
                $pointsEarned = ceil($pointsEarned * 1.2);
            }
            
            // Add points to user (will be added after successful payment)
            // For now, we'll store it in a session or calculate later
            
            DB::commit();
            
            return redirect()->route('pembeli.transactions')->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran dalam waktu 1 menit untuk menyelesaikan transaksi.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Calculate point redemption discount
     */
    public function calculatePointDiscount(Request $request)
    {
        try {
            $request->validate([
                'points' => 'required|integer|min:0',
            ]);
            
            $pembeli = Auth::guard('pembeli')->user();
            $points = $request->points;
            
            // Debug logging
            \Log::info('Point calculation request', [
                'user_id' => $pembeli->ID_PEMBELI,
                'requested_points' => $points,
                'available_points' => $pembeli->POINT_PEMBELI
            ]);
            
            if ($points > $pembeli->POINT_PEMBELI) {
                return response()->json([
                    'success' => false,
                    'message' => 'Poin yang ditukar melebihi poin yang Anda miliki.'
                ], 400);
            }
            
            // Calculate discount (1 point = Rp 1,000)
            $discount = $points * 1000;
            
            // Calculate cart subtotal
            $cartItems = KeranjangPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                        ->whereDoesntHave('transaksi')
                                        ->with('barang')
                                        ->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang kosong.'
                ], 400);
            }
                                        
            $subtotal = $cartItems->sum(function($item) {
                return $item->barang->HARGA;
            });
            
            $shippingCost = $subtotal >= 1500000 ? 0 : 100000;
            $total = $subtotal + $shippingCost - $discount;
            
            // Ensure total doesn't go below 0
            $total = max(0, $total);
            
            return response()->json([
                'success' => true,
                'discount' => $discount,
                'remaining_points' => $pembeli->POINT_PEMBELI - $points,
                'total' => $total,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'formattedDiscount' => 'Rp ' . number_format($discount, 0, ',', '.'),
                'formattedTotal' => 'Rp ' . number_format($total, 0, ',', '.'),
                'formattedSubtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'formattedShipping' => $shippingCost === 0 ? 'Gratis' : 'Rp ' . number_format($shippingCost, 0, ',', '.')
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang dikirim tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error calculating point discount', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung diskon poin.'
            ], 500);
        }
    }

    public function recalculateCheckout(Request $request)
    {
        $request->validate([
            'redeemed_points' => 'nullable|integer|min:0',
            'shipping_method' => 'nullable|in:kurir,ambil_sendiri'
        ]);
        
        $pembeli = Auth::guard('pembeli')->user();
        $cartItems = KeranjangPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                    ->whereDoesntHave('transaksi')
                                    ->with('barang')
                                    ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('pembeli.cart.index')->with('error', 'Keranjang Anda kosong.');
        }
        
        $alamat = AlamatPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)->get();
        $defaultAlamat = AlamatPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                    ->where('IS_DEFAULT', true)
                                    ->first();
        
        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->barang->HARGA;
        });
        
        $redeemedPoints = $request->redeemed_points ?? 0;
        $shippingMethod = $request->shipping_method ?? 'kurir';
        
        // Validate points
        if ($redeemedPoints > $pembeli->POINT_PEMBELI) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Poin yang ditukar melebihi poin yang Anda miliki.');
        }
        
        // Calculate shipping
        $shippingCost = 0;
        if ($shippingMethod == 'kurir') {
            $shippingCost = $subtotal >= 1500000 ? 0 : 100000;
        }
        
        // Calculate discount
        $pointDiscount = $redeemedPoints * 10000;
        $total = $subtotal + $shippingCost - $pointDiscount;
        
        // Calculate potential points earned
        $pointsEarned = floor($subtotal / 10000);
        if ($subtotal > 500000) {
            $pointsEarned = ceil($pointsEarned * 1.2);
        }
        
        return view('pembeli.cart.checkout', compact(
            'cartItems', 
            'alamat', 
            'defaultAlamat', 
            'subtotal', 
            'shippingCost', 
            'total', 
            'pembeli',
            'pointsEarned',
            'redeemedPoints',
            'pointDiscount',
            'shippingMethod'
        ));
    }

}