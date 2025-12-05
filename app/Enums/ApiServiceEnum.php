<?php

namespace App\Enums;

enum ApiServiceEnum: string
{
    case C2D = 'Chat2Desk';
    case GPT = 'ChatGPT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
