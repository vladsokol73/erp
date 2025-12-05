<?php

namespace App\Http\Controllers;

use App\Contracts\Webhook\TelegramIntegrationManager;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private readonly TelegramIntegrationManager $telegramIntegrationManager
    ) {}

    public function tgBot(Request $request)
    {
        $payload = $request->all();
        Log::info('[Webhook] telegram update: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));

        $text = (string) data_get($payload, 'message.text', '');
        if ($text === '') {
            return ApiResponse::success();
        }

        if (preg_match('/^\/start\s+(key-[a-zA-Z0-9]+)$/i', trim($text), $matches)) {
            $key = $matches[1];
            $ok = $this->telegramIntegrationManager->connectByStartKey($key, $payload);
            if ($ok) {
                return ApiResponse::successMessage('connected');
            }
            return ApiResponse::notFound('Integration key not found');
        }

        Log::warning('[Webhook] unexpected message format: ' . $text);
        return ApiResponse::badRequest('Unexpected message');
    }
}


