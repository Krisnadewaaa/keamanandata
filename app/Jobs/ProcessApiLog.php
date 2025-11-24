<?php

namespace App\Jobs;

use App\Models\ApiLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\SecurityAlertNotification;
use Illuminate\Support\Facades\Notification;

class ProcessApiLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $log;

    // configuration thresholds (bisa diatur di .env atau config)
    public $thresholdWindowMin = 5; // window minutes
    public $thresholdAttempts = 20; // attempts threshold to consider blocking
    public $blockDurationMinutes = 60; // block duration in minutes

    public function __construct(ApiLog $log)
    {
        $this->log = $log;
    }

    public function handle()
    {
        $ip = $this->log->ip_address;
        $status = (int) $this->log->status_code;

        // 1) jika status 401/403/tidak sah -> hitung jumlah dalam window
        if (in_array($status, [401,403])) {
            $since = Carbon::now()->subMinutes($this->thresholdWindowMin);
            $count = ApiLog::where('ip_address', $ip)
                            ->whereIn('status_code', [401,403])
                            ->where('created_at', '>=', $since)
                            ->count();

            // update attempts di blocked_ips (upsert)
            DB::table('blocked_ips')->updateOrInsert(
                ['ip_address' => $ip],
                [
                    'attempts' => DB::raw("COALESCE(attempts,0) + 1"),
                    'updated_at' => Carbon::now()
                ]
            );

            // jika melewati threshold, lakukan block & buat alert & kirim notifikasi
            if ($count >= $this->thresholdAttempts) {
                // block IP
                $blockedUntil = Carbon::now()->addMinutes($this->blockDurationMinutes);
                DB::table('blocked_ips')->where('ip_address', $ip)->update([
                    'blocked_until' => $blockedUntil,
                    'reason' => 'Multiple 401/403 within short period (auto-block)',
                    'updated_at' => Carbon::now()
                ]);

                // create alert
                $alertId = DB::table('alerts')->insertGetId([
                    'api_log_id' => $this->log->id,
                    'ip_address' => $ip,
                    'type' => 'brute_force',
                    'message' => "Detected {$count} unauthorized responses (401/403) from {$ip} within {$this->thresholdWindowMin} minutes. IP auto-blocked until {$blockedUntil}.",
                    'severity' => 'high',
                    'meta' => json_encode(['count' => $count, 'window_min' => $this->thresholdWindowMin]),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                // Notify admin(s) via Notification channel (email + telegram)
                // Configure recipients in config or .env; for simplicity, use DB or env
                // Example: NOTIFY_EMAILS=admin1@example.com,admin2@example.com
                $emails = array_filter(array_map('trim', explode(',', env('NOTIFY_EMAILS', ''))));
                $telegramChatIds = array_filter(array_map('trim', explode(',', env('NOTIFY_TELEGRAM_CHATIDS', ''))));

                $notification = new SecurityAlertNotification([
                    'title' => 'Auto-block triggered',
                    'message' => "IP {$ip} auto-blocked ({$count} failed auths).",
                    'alert_id' => $alertId,
                    'meta' => ['ip' => $ip, 'count' => $count]
                ]);

                // send email notifications
                foreach ($emails as $mail) {
                    Notification::route('mail', $mail)->notify($notification);
                }

                // send telegram notifications
                foreach ($telegramChatIds as $chatId) {
                    Notification::route('telegram', $chatId)->notify($notification);
                }
            }
        }

        // 2) Additional detection: very slow endpoints (duration_ms threshold)
        if ($this->log->duration_ms > 2000) { // >2s
            DB::table('alerts')->insert([
                'api_log_id' => $this->log->id,
                'ip_address' => $this->log->ip_address,
                'type' => 'slow_response',
                'message' => "Slow response detected ({$this->log->duration_ms} ms) at endpoint {$this->log->endpoint}.",
                'severity' => 'medium',
                'meta' => json_encode(['duration' => $this->log->duration_ms]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        // add other rules as needed...
    }
}
