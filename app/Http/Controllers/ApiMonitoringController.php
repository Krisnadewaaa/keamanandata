<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Alert;

class ApiMonitoringController extends Controller
{
    public function index()
    {
        $totalRequest = ApiLog::count();
        $failedRequest = ApiLog::where('status_code', '>=', 400)->count();
        $suspicious = ApiLog::whereIn('status_code', [401,403])->count();

        $recentLogs = ApiLog::orderBy('created_at', 'DESC')->limit(30)->get();

        $topEndpoints = ApiLog::selectRaw('endpoint, COUNT(*) as total')
                        ->groupBy('endpoint')
                        ->orderBy('total', 'DESC')
                        ->limit(5)
                        ->get();

        $alerts = DB::table('alerts')->orderBy('created_at', 'DESC')->limit(50)->get();

        return view('admin.monitoring.index', compact(
            'totalRequest', 'failedRequest', 'suspicious', 'recentLogs', 'topEndpoints', 'alerts'
        ));
    }

    // AJAX endpoint: returns counts per hour for last 24 hours
    public function chartPerHour(Request $request)
    {
        $hours = [];
        $counts = [];
        for ($i = 23; $i >= 0; $i--) {
            $time = Carbon::now()->subHours($i);
            $label = $time->format('H:00');
            $start = $time->copy()->startOfHour();
            $end = $time->copy()->endOfHour();

            $count = ApiLog::whereBetween('created_at', [$start, $end])->count();
            $hours[] = $label;
            $counts[] = $count;
        }

        return response()->json(['labels' => $hours, 'data' => $counts]);
    }

    // AJAX endpoint: returns counts per day for last N days (default 7)
    public function chartPerDay(Request $request)
    {
        $days = intval($request->get('days', 7));
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i);
            $labels[] = $d->format('Y-m-d');
            $data[] = ApiLog::whereDate('created_at', $d->toDateString())->count();
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    // list alerts (optional single page)
    public function alerts()
    {
        $alerts = DB::table('alerts')->orderBy('created_at', 'desc')->paginate(30);
        return view('admin.monitoring.alerts', compact('alerts'));
    }

    // mark alert as read
    public function markAlertRead($id)
    {
        DB::table('alerts')->where('id', $id)->update(['read' => 1, 'updated_at' => now()]);
        return redirect()->back()->with('success', 'Alert marked as read.');
    }

    // manual block ip
    public function blockIp(Request $request)
    {
        $ip = $request->ip_to_block ?? null;
        if (!$ip) return redirect()->back()->with('error','IP tidak diberikan.');

        DB::table('blocked_ips')->updateOrInsert(
            ['ip_address' => $ip],
            ['reason' => $request->reason ?? 'manual block', 'blocked_until' => $request->blocked_until ?? null, 'updated_at' => now()]
        );

        return redirect()->back()->with('success', "IP {$ip} diblokir.");
    }

    // manual unblock ip
    public function unblockIp($ip)
    {
        DB::table('blocked_ips')->where('ip_address', $ip)->delete();
        return redirect()->back()->with('success', "IP {$ip} telah dibuka blokirnya.");
    }
}
