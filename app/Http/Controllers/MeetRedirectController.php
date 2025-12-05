<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Str;

class MeetRedirectController extends Controller
{
    public function guest(Request $request, ?string $room = null): JsonResponse
    {
        $rawRoom = $room ?? '';
        $sanitizedRoom = $rawRoom !== '' ? (preg_replace('/[^A-Za-z0-9_-]/', '', $rawRoom) ?? '') : '';
        if ($sanitizedRoom === '') {
            Log::error('MeetRedirectController.guest: empty room provided');
            return ApiResponse::validationMessage('Встреча не найдена или срок истек');
        }

        // Разрешаем вход как гость ТОЛЬКО в комнаты, существующие в кеше
        if (!cache()->has('meet:room:'.$sanitizedRoom)) {
            Log::error('MeetRedirectController.guest: room not found or expired "'.$sanitizedRoom.'"');
            return ApiResponse::notFound('Встреча не найдена или срок истек');
        }

        // Базовый URL Jitsi берём из конфига
        $base = rtrim((string) config('jitsi.base', 'https://meet.investingindigital.com'), '/');
        if ($base === '' || $base === 'https://meet.example.com') {
            Log::error('MeetRedirectController.guest: missing or default JITSI_BASE');
            return ApiResponse::serverError('Jitsi base URL is not configured');
        }

        try {
            $target = "{$base}/{$sanitizedRoom}";
            Log::info('MeetRedirectController.guest: redirect guest to '.$target);

            return ApiResponse::success([
                'join_url' => $target,
            ]);
        } catch (\Throwable $e) {
            Log::error('MeetRedirectController.guest: redirect failed: '.$e->getMessage());
            return ApiResponse::serverError('Не удалось сгенерировать ссылку на встречу');
        }
    }


    public function go(Request $request, ?string $room = null): RedirectResponse
    {
        $user = Auth::user();
        Log::info('MeetRedirectController: start {"room":"'.($room.'"').',"user_id":"'.($user->id ?? 'guest').'"}');

        // Генерация или очистка имени комнаты
        $sanitizedRoom = '';
        if ($room !== null) {
            $sanitizedRoom = preg_replace('/[^A-Za-z0-9_-]/', '', $room) ?? '';
            if ($sanitizedRoom === '') {
                Log::error('MeetRedirectController: invalid room name "'.$room.'"');
                abort(404);
            }
        } else {
            $sanitizedRoom = $this->generateUniqueRoomName();
            Log::info('MeetRedirectController: generated room "'.$sanitizedRoom.'"');
        }

        $now = time();
        // TTL токена берём из конфига (по умолчанию 10 минут)
        $ttlSeconds = (int) config('jitsi.jwt_ttl', 10 * 60);

        $payload = [
            // Значения для JWT берём из конфига
            'aud' => config('jitsi.aud', 'jitsi'),
            'iss' => config('jitsi.iss', 'erp-auth'),
            'sub' => config('jitsi.sub', 'meet.example.com'),
            'room' => $sanitizedRoom,
            'nbf' => $now - 5,
            'exp' => $now + $ttlSeconds,
            'context' => [
                'user' => [
                    'name' => $user->name ?? 'User',
                    'email' => $user->email ?? null,
                    'affiliation' => ($user->is_admin ?? false) ? 'owner' : 'member',
                ],
            ],
            'moderator' => (bool)($user->is_admin ?? false),
        ];

        // Секрет подписи берём из конфига
        $secret = config('jitsi.hs256_secret');
        if (empty($secret)) {
            Log::error('MeetRedirectController: missing JITSI_HS256_SECRET');
            abort(500, 'Jitsi JWT secret is not configured');
        }
        try {
            $token = JWT::encode($payload, $secret, 'HS256');
        } catch (\Throwable $e) {
            Log::error('MeetRedirectController: JWT encode failed: '.$e->getMessage());
            abort(500, 'Failed to encode JWT');
        }

        // Базовый URL Jitsi берём из конфига
        $base = rtrim((string) config('jitsi.base', 'https://meet.investingindigital.com'), '/');
        if ($base === '' || $base === 'https://meet.example.com') {
            Log::error('MeetRedirectController: missing or default JITSI_BASE');
            abort(500, 'Jitsi base URL is not configured');
        }

        $target = "{$base}/{$sanitizedRoom}?jwt={$token}";
        Log::info('MeetRedirectController: redirect to '.$target);
        return redirect()->away($target);
    }

    private function generateUniqueRoomName(): string
    {
        // Формат xxx-xxxx-xxx (a-z0-9)
        $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $random = function (int $len) use ($alphabet) {
            $s = '';
            $max = strlen($alphabet) - 1;
            for ($i = 0; $i < $len; $i++) {
                $s .= $alphabet[random_int(0, $max)];
            }
            return $s;
        };

        // Ключ кеша со множеством комнат
        $cacheKey = 'meet:rooms:index';
        $ttlSeconds = 2 * 60 * 60; // 2 часа

        // Получим текущий список
        $existing = cache()->get($cacheKey, []);
        if (!is_array($existing)) {
            $existing = [];
        }

        // Поиск уникального
        for ($attempt = 0; $attempt < 30; $attempt++) {
            $candidate = $random(3).'-'.$random(4).'-'.$random(3);
            if (!isset($existing[$candidate])) {
                // Добавим в индекс и поставим TTL
                $existing[$candidate] = time();
                cache()->put($cacheKey, $existing, $ttlSeconds);
                // Также отдельный ключ комнаты, если нужно быстро проверять TTL по ней
                cache()->put('meet:room:'.$candidate, 1, $ttlSeconds);
                return $candidate;
            }
        }

        // Фоллбек: если вдруг 30 коллизий подряд
        return $random(3).'-'.$random(4).'-'.$random(3);
    }
}
