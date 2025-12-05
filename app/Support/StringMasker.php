<?php

namespace App\Support;

class StringMasker
{
    public static function blurString(string $token, int $visibleStart = 3, int $visibleEnd = 3): string
    {
        $length = mb_strlen($token);

        // For very short tokens, keep full blur of original length
        if ($length <= ($visibleStart + $visibleEnd)) {
            return str_repeat('*', $length);
        }

        $start = mb_substr($token, 0, $visibleStart);
        $end = mb_substr($token, -$visibleEnd);

        // Always use fixed 5 asterisks in the middle to limit visual width
        $masked = str_repeat('*', 5);

        return $start . $masked . $end;
    }
}
