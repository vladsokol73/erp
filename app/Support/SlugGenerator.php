<?php

namespace App\Support;

use Illuminate\Support\Str;

class SlugGenerator
{
    /**
     * Генерирует slug из строки.
     *
     * @param string $text
     * @return string
     */
    public static function generate(string $text): string
    {
        return Str::slug($text, separator: '-');
    }
}
