<?php

return [
    // Базовый URL инстанса Jitsi Meet (без завершающего слеша)
    'base' => env('JITSI_BASE', 'https://meet.investingindigital.com'),

    // Параметры JWT
    'aud' => env('JITSI_AUD', 'jitsi'),
    'iss' => env('JITSI_ISS', 'erp-auth'),
    'sub' => env('JITSI_SUB', 'meet.example.com'),

    // Секрет для подписи JWT (HS256)
    'hs256_secret' => env('JITSI_HS256_SECRET'),

    // Время жизни JWT (в секундах). Опционально, можно использовать в контроллере.
    // По умолчанию 10 минут.
    'jwt_ttl' => env('JITSI_JWT_TTL', 10 * 60),
];
