<?php
// File: app/Http/Controllers/Pembeli/TransactionPaymentController.php (Updated)

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pembeli;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TransactionPaymentController extends Controller
{
    /**
     * Show payment form for a transaction
     */
    public function showPaymentForm($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $transaction = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                              ->where('ID_TRANSAKSI', $id)
                              ->where('STATUS_TRANSAKSI', 'Menunggu Pembayaran')
                              ->with('barang')
                              ->firstOrFail();
        
        // Check if payment has expired and auto-cancel
        if ($transaction->isPaymentExpired()) {
            Transaksi::cancelTransaction($id);
            return redirect()->route('pembeli.transactions')
                           ->with('error', 'Waktu pembayaran telah habis. Transaksi dibatalkan dan poin/stok dikembalikan.');
        }
        
        return view('pembeli.payment.form', compact('transaction', 'pembeli'));
    }
    
    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $pembeli = Auth::guard('pembeli')->user();
        $transaction = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                              ->where('ID_TRANSAKSI', $id)
                              ->where('STATUS_TRANSAKSI', 'Menunggu Pembayaran')
                              ->firstOrFail();
        
        // Check if payment has expired
        if ($transaction->isPaymentExpired()) {
            Transaksi::cancelTransaction($id);
            return redirect()->route('pembeli.transactions')
                           ->with('error', 'Waktu pembayaran telah habis. Transaksi dibatalkan dan poin/stok dikembalikan.');
        }
        
        // Upload payment proof
        if ($request->hasFile('payment_proof')) {
            try {
                // Delete old payment proof if exists
                if ($transaction->BUKTI_PEMBAYARAN && file_exists(public_path($transaction->BUKTI_PEMBAYARAN))) {
                    unlink(public_path($transaction->BUKTI_PEMBAYARAN));
                }
                
                $file = $request->file('payment_proof');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Create directory if it doesn't exist
                if (!file_exists(public_path('images/payment_proofs'))) {
                    mkdir(public_path('images/payment_proofs'), 0755, true);
                }
                
                $file->move(public_path('images/payment_proofs'), $filename);
                
                // Update transaction
                $transaction->BUKTI_PEMBAYARAN = 'images/payment_proofs/' . $filename;
                $transaction->STATUS_TRANSAKSI = 'Menunggu Konfirmasi';
                $transaction->TANGGAL_UPLOAD_BUKTI = Carbon::now();
                $transaction->save();
                
                return redirect()->route('pembeli.transaction.detail', $id)
                               ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi dari CS.');
                               
            } catch (\Exception $e) {
                Log::error("Error uploading payment proof for transaction {$id}: " . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupload bukti pembayaran.');
            }
        }
        
        return redirect()->back()->with('error', 'Gagal mengupload bukti pembayaran.');
    }
    
    /**
     * Get remaining payment time (AJAX)
     */
    public function getRemainingTime($id)
    {
        try {
            $pembeli = Auth::guard('pembeli')->user();
            $transaction = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                  ->where('ID_TRANSAKSI', $id)
                                  ->first();
            
            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }
            
            $remainingSeconds = $transaction->getRemainingPaymentTimeInSeconds();
            $remainingMinutes = $transaction->getRemainingPaymentTime();
            $expired = $transaction->isPaymentExpired();
            
            // Auto-cancel if expired
            if ($expired && $transaction->STATUS_TRANSAKSI === 'Menunggu Pembayaran') {
                Transaksi::cancelTransaction($id);
            }
            
            return response()->json([
                'remaining_seconds' => $remainingSeconds,
                'remaining_minutes' => $remainingMinutes,
                'expired' => $expired,
                'formatted_time' => $transaction->getFormattedRemainingTime()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error getting remaining time for transaction {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Check transaction status (AJAX)
     */
    public function getTransactionStatus($id)
    {
        try {
            $pembeli = Auth::guard('pembeli')->user();
            $transaction = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                  ->where('ID_TRANSAKSI', $id)
                                  ->first();
            
            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }
            
            // Auto-cancel if expired
            if ($transaction->STATUS_TRANSAKSI === 'Menunggu Pembayaran' && $transaction->isPaymentExpired()) {
                Transaksi::cancelTransaction($id);
                $transaction->refresh();
            }
            
            return response()->json([
                'status' => $transaction->STATUS_TRANSAKSI,
                'expired' => $transaction->isPaymentExpired(),
                'remaining_seconds' => $transaction->getRemainingPaymentTimeInSeconds(),
                'can_be_paid' => $transaction->canBePaid()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error checking status for transaction {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Cancel expired transaction (AJAX)
     */
    public function cancelExpiredTransaction($id)
    {
        try {
            $pembeli = Auth::guard('pembeli')->user();
            $transaction = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                  ->where('ID_TRANSAKSI', $id)
                                  ->first();
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan.'
                ], 404);
            }
            
            // Check if transaction is in correct status
            if ($transaction->STATUS_TRANSAKSI !== 'Menunggu Pembayaran') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak dapat dibatalkan karena status tidak sesuai.'
                ], 400);
            }
            
            // Check if really expired
            if (!$transaction->isPaymentExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi belum melewati batas waktu.'
                ], 400);
            }
            
            // Cancel the transaction
            $cancelled = Transaksi::cancelTransaction($id);
            
            if ($cancelled) {
                $pointsReturned = $transaction->POIN_DITUKAR ?? 0;
                $productName = $transaction->barang->NAMA_BARANG ?? 'Produk';
                
                return response()->json([
                    'success' => true,
                    'message' => "Transaksi berhasil dibatalkan. " . 
                               ($pointsReturned > 0 ? "{$pointsReturned} poin dikembalikan dan " : "") .
                               "stok {$productName} dikembalikan."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membatalkan transaksi. Silakan coba lagi.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error("Error cancelling expired transaction {$id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
}