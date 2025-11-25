<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Auth;

class ApiLogger
{
    public function handle($request, Closure $next)
    {
        \Log::info('ApiLogger executed for path: '.$request->path());

        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000;

        try { 
            ApiLog::create([
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'user_id' => $user->id ?? null,
                'role' => $user->role->NAMA_ROLE ?? null,
                'duration_ms' => $duration,
            ]);
        } catch (\Exception $e) {
            \Log::error("GAGAL INSERT API LOG", [
                'error' => $e->getMessage()
            ]);
        }

        return $response;
    }
}
