<?php

namespace App\Services\Webhook;

use App\Contracts\Webhook\TelegramIntegrationManager;
use App\Models\TelegramIntegration;
use App\Notifications\TicketNotification;
use Carbon\Carbon;

class EloquentTelegramIntegrationManager implements TelegramIntegrationManager
{
    public function connectByStartKey(string $key, array $update): bool
    {
        $integration = TelegramIntegration::query()->where('key', $key)->first();
        if (!$integration) {
            return false;
        }

        $chatId = data_get($update, 'message.chat.id');
        if (!$chatId) {
            return false;
        }

        $integration->update([
            'tg_id' => $chatId,
            'activated_at' => Carbon::now(),
        ]);

        $user = $integration->user;
        if ($user) {
            $user->notify(new TicketNotification('connected'));
        }
        return true;
    }
}


