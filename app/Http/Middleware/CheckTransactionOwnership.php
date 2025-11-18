<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class CheckTransactionOwnership
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $transactionId = $request->route('id');
        $pembeli = Auth::guard('pembeli')->user();
        
        if ($transactionId) {
            $transaction = Transaksi::where('ID_TRANSAKSI', $transactionId)
                                  ->where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                  ->first();
            
            if (!$transaction) {
                return redirect()->route('pembeli.transactions')
                               ->with('error', 'Transaksi tidak ditemukan atau bukan milik Anda.');
            }
        }
        
        return $next($request);
    }
}