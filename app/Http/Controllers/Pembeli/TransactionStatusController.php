<?php
// File: app/Http/Controllers/Pembeli/TransactionStatusController.php (Enhanced)

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionStatusController extends Controller
{
    /**
     * Check and cancel expired transactions (called via AJAX)
     */
    public function checkExpiredTransactions()
    {
        // Cancel expired transactions
        $cancelledCount = Transaksi::cancelExpiredTransactions();
        
        return response()->json([
            'status' => 'checked', 
            'cancelled' => $cancelledCount,
            'timestamp' => Carbon::now()->toISOString()
        ]);
    }
    
    /**
     * Get transaction status with enhanced information (called via AJAX)
     */
    public function getTransactionStatus($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $transaction = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                              ->where('ID_TRANSAKSI', $id)
                              ->first();
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        
        $remainingTime = $transaction->getRemainingPaymentTime();
        $remainingSeconds = $transaction->getRemainingPaymentTimeInSeconds();
        $isExpired = $transaction->isPaymentExpired();
        
        // If expired but status still "Menunggu Pembayaran", cancel it
        if ($isExpired && $transaction->STATUS_TRANSAKSI === 'Menunggu Pembayaran') {
            Transaksi::cancelTransaction($id);
            $transaction->refresh(); // Reload the model
        }
        
        return response()->json([
            'status' => $transaction->STATUS_TRANSAKSI,
            'remaining_time' => $remainingTime,
            'remaining_seconds' => $remainingSeconds,
            'formatted_time' => $transaction->getFormattedRemainingTime(),
            'expired' => $isExpired,
            'verification_status' => $transaction->STATUS_VERIFIKASI ?? 'Pending',
            'can_pay' => $transaction->canBePaid(),
            'can_cancel' => $transaction->canBeCancelled(),
            'timestamp' => Carbon::now()->toISOString()
        ]);
    }
    
    /**
     * Cancel specific transaction manually
     */
    public function cancelTransaction($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $transaction = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                              ->where('ID_TRANSAKSI', $id)
                              ->first();
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        
        if (!$transaction->canBeCancelled()) {
            return response()->json(['error' => 'Transaction cannot be cancelled'], 400);
        }
        
        $success = Transaksi::cancelTransaction($id);
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'Transaksi berhasil dibatalkan' : 'Gagal membatalkan transaksi'
        ]);
    }
}
