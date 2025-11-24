<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Http;

class SecurityAlertNotification extends Notification
{
    use Queueable;

    protected $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // telegram handled manually in toTelegram below via Notification::route
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->payload['title'] ?? 'Security Alert')
                    ->line($this->payload['message'] ?? '')
                    ->action('Open Monitoring', url(route('admin.monitoring')))
                    ->line('This is an automated alert.');
    }

    // For Telegram we will also implement toDatabase; but actual Telegram route uses a custom channel:
    public function toArray($notifiable)
    {
        return $this->payload;
    }

    // Add helper to send to Telegram (Notification::route('telegram', $chatId)->notify($notification))
    public function toTelegram($notifiable)
    {
        // $notifiable is chat id in our Notification::route call
        $chatId = $notifiable;
        $text = "*{$this->payload['title']}*\n{$this->payload['message']}\n".(isset($this->payload['meta']) ? json_encode($this->payload['meta']) : '');

        $botToken = env('TELEGRAM_BOT_TOKEN');
        if (!$botToken || !$chatId) {
            return;
        }

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
}
