<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckBlockedIp
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        $row = DB::table('blocked_ips')->where('ip_address', $ip)->first();
        if ($row) {
            if ($row->blocked_until === null || Carbon::now()->lt(Carbon::parse($row->blocked_until))) {
                // Kalau masih diblokir, langsung return 403
                return response()->json([
                    'message' => 'Your IP has been temporarily blocked due to suspicious activity.'
                ], 403);
            } else {
                // expired, hapus atau reset
                DB::table('blocked_ips')->where('id', $row->id)->delete();
            }
        }

        return $next($request);
    }
}
