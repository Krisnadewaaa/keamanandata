<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PegawaiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')
                            ->with('error', 'Anda harus login sebagai pegawai untuk mengakses halaman ini.');
        }

        // If role is specified, check if the user has that role
        if ($role) {
            $user = Auth::guard('pegawai')->user();
            
            switch ($role) {
                case 'owner':
                    if (!$user->isOwner()) {
                        return redirect()->back()
                                        ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
                    }
                    break;
                
                case 'admin':
                    if (!$user->isAdmin()) {
                        return redirect()->back()
                                        ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
                    }
                    break;
                
                case 'gudang':
                    if (!$user->isGudang()) {
                        return redirect()->back()
                                        ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
                    }
                    break;
                
                case 'cs':
                    if (!$user->isCS()) {
                        return redirect()->back()
                                        ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
                    }
                    break;
                
                case 'kurir':
                    if (!$user->isKurir()) {
                        return redirect()->back()
                                        ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
                    }
                    break;
                
                case 'hunter':
                    if (!$user->isHunter()) {
                        return redirect()->back()
                                        ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
                    }
                    break;
                
                default:
                    // No specific role required
                    break;
            }
        }
        
        return $next($request);
    }
}