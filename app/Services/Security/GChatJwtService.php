<?php

namespace App\Services\Security;

use App\Contracts\Security\JwtEncoder;
use Illuminate\Support\Facades\Config;

class GChatJwtService implements JwtEncoder
{
    public function encode(array $payload): string
    {
        $secret = Config::get('gchat.secret');
        if (!$secret) {
            throw new \RuntimeException('GCHAT_HS256_SECRET is not configured');
        }

        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $segments = [];
        $segments[] = $this->urlsafeB64Encode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $segments[] = $this->urlsafeB64Encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $secret, true);
        $segments[] = $this->urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    private function urlsafeB64Encode(string $input): string
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}


