<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class TelegramChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toTelegram')) {
            return;
        }

        $message = $notification->toTelegram($notifiable);
        if (!$message) {
            return;
        }

        foreach ($notifiable->telegramIntegrations as $integration) {
            $chatId = $integration->tg_id;
            $token = config('services.telegram.bot_token');

            Http::get("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => (int)$chatId,
                'text' => $message,
            ]);
        }
    }
}
