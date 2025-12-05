<?php

namespace App\Services\Telegram;

use App\Models\TelegramIntegration;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TelegramIntegrationService
{
    /**
     * Получить существующий или создать новый key для пользователя.
     */
    public function getOrCreateKeyForUser(?User $user = null): string
    {
        $user = $user ?? Auth::user();

        $integration = TelegramIntegration::firstWhere('user_id', $user->id);
        if ($integration) {
            return $integration->key;
        }

        $key = $this->generateUniqueKey(6);

        $user->telegramIntegrations()->create([
            'key' => $key,
        ]);

        return $key;
    }

    /**
     * Удалить интеграцию Telegram для пользователя.
     */
    public function destroyForUser(?User $user = null): void
    {
        $user = $user ?? Auth::user();

        TelegramIntegration::where('user_id', $user->id)->delete();
    }

    public function checkTelegramIntegration(?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        return $user->activeTelegramIntegrations();
    }

    /**
     * Сгенерировать уникальный key с префиксом.
     * @param int $length — длина случайной части (без префикса)
     * @param string $prefix — префикс ключа
     */
    private function generateUniqueKey(int $length = 10, string $prefix = 'key-'): string
    {
        do {
            // Строчные латинские буквы + цифры
            $random = Str::lower(Str::random($length));
            $candidate = $prefix . $random;
        } while (TelegramIntegration::where('key', $candidate)->exists());

        return $candidate;
    }
}
