<?php

namespace App\Services\Meet;

use App\DTO\Meet\MeetRoomDto;
use App\Models\Log;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;

class MeetService
{
    private int $roomTtlSeconds = 2 * 60 * 60; // 2 часа

    public function generateRoomForUser(int $userId): string
    {
        $room = $this->generateRoom($userId);
        $this->addRoomToUserHistory($userId, $room);
        return $room;
    }

    public function generateRoom(?int $creatorId = null): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $random = function (int $len) use ($alphabet) {
            $s = '';
            $max = strlen($alphabet) - 1;
            for ($i = 0; $i < $len; $i++) {
                $s .= $alphabet[random_int(0, $max)];
            }
            return $s;
        };

        $indexKey = 'meet:rooms:index';
        $existing = Cache::get($indexKey, []);
        if (!is_array($existing)) {
            $existing = [];
        }

        for ($attempt = 0; $attempt < 30; $attempt++) {
            $candidate = $random(3).'-'.$random(4).'-'.$random(3);
            if (!isset($existing[$candidate])) {
                $this->cacheRoom($candidate, $creatorId);
                Log::info('MeetService: generated room "'.$candidate.'"');
                return $candidate;
            }
        }

        $fallback = $random(3).'-'.$random(4).'-'.$random(3);
        $this->cacheRoom($fallback, $creatorId);
        Log::info('MeetService: generated room (fallback) "'.$fallback.'"');
        return $fallback;
    }

    public function cacheRoom(string $room, ?int $creatorId = null): void
    {
        $indexKey = 'meet:rooms:index';
        $existing = Cache::get($indexKey, []);
        if (!is_array($existing)) {
            $existing = [];
        }
        $existing[$room] = time();
        Cache::put($indexKey, $existing, $this->roomTtlSeconds);
        $expiresAt = time() + $this->roomTtlSeconds;
        Cache::put('meet:room:'.$room, ['expires_at' => $expiresAt, 'creator_id' => $creatorId], $this->roomTtlSeconds);
    }

    public function roomExists(string $room): bool
    {
        return Cache::has('meet:room:'.$room);
    }

    public function listUserRooms(int $userId): array
    {
        $key = 'meet:user:'.$userId.':rooms';
        $list = Cache::get($key, []);
        if (!is_array($list)) {
            $list = [];
        }

        // Фильтруем только актуальные комнаты
        $result = [];
        foreach ($list as $item) {
            $room = $item['room'] ?? null;
            $createdAt = (int)($item['created_at'] ?? 0);
            if (!is_string($room) || $room === '') {
                continue;
            }
            if (!$this->roomExists($room)) {
                continue;
            }
            $roomMeta = Cache::get('meet:room:'.$room, null);
            $ttlRemaining = null;
            if (is_array($roomMeta) && isset($roomMeta['expires_at'])) {
                $ttlRemaining = max(0, $roomMeta['expires_at'] - time());
            }
            $result[] = new MeetRoomDto(
                room: $room,
                created_at: $createdAt,
                ttl_remaining: $ttlRemaining,
            );
        }

        // Перезаписываем очищенный список (продлеваем общий TTL истории)
        Cache::put(
            $key,
            array_map(
                fn(MeetRoomDto $r) => [
                    'room' => $r->room,
                    'created_at' => $r->created_at,
                ],
                $result
            ),
            $this->roomTtlSeconds
        );

        return $result;
    }

    private function addRoomToUserHistory(int $userId, string $room): void
    {
        $key = 'meet:user:'.$userId.':rooms';
        $list = Cache::get($key, []);
        if (!is_array($list)) {
            $list = [];
        }
        // Исключаем дубли
        $list = array_values(array_filter($list, fn($i) => ($i['room'] ?? null) !== $room));
        $list[] = [
            'room' => $room,
            'created_at' => time(),
        ];
        // Ограничение длины истории (например, 50)
        if (count($list) > 50) {
            $list = array_slice($list, -50);
        }
        Cache::put($key, $list, $this->roomTtlSeconds);
    }

    public function buildJoinUrl(object $user, string $room): string
    {
        $now = time();
        $ttlSeconds = 10 * 60;

        $roomMeta = Cache::get('meet:room:'.$room, []);
        $creatorId = (int)($roomMeta['creator_id'] ?? 0);
        $isModerator = $user && isset($user->id) && (int)$user->id === $creatorId;

        $payload = [
            'aud' => env('JITSI_AUD', 'jitsi'),
            'iss' => env('JITSI_ISS', 'erp-auth'),
            'sub' => env('JITSI_SUB', 'meet.example.com'),
            'room' => $room,
            'nbf' => $now - 5,
            'exp' => $now + $ttlSeconds,
            'context' => [
                'user' => [
                    'name' => $user->name ?? 'User',
                    'email' => $user->email ?? null,
                ],
            ],
            'moderator' => $isModerator,
        ];

        $secret = env('JITSI_HS256_SECRET');
        if (empty($secret)) {
            Log::error('MeetService: missing JITSI_HS256_SECRET');
            throw new \RuntimeException('Jitsi JWT secret is not configured');
        }
        $token = JWT::encode($payload, $secret, 'HS256');

        $base = rtrim(env('JITSI_BASE', 'https://meet.example.com'), '/');
        if ($base === '' || $base === 'https://meet.example.com') {
            Log::error('MeetService: missing or default JITSI_BASE');
            throw new \RuntimeException('Jitsi base URL is not configured');
        }

        return "{$base}/{$room}?jwt={$token}";
    }

    public function getRoomTtlSeconds(): int
    {
        return $this->roomTtlSeconds;
    }
}
