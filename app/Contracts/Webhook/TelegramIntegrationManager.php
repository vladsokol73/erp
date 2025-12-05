<?php

namespace App\Contracts\Webhook;

interface TelegramIntegrationManager
{
    /**
     * Connect a Telegram chat to integration by /start key payload.
     * Returns true on success, false if key invalid or already connected.
     */
    public function connectByStartKey(string $key, array $update): bool;
}


