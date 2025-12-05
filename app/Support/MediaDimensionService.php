<?php


namespace App\Support;

class MediaDimensionService
{
    /**
     * Вычисляет соотношение сторон из строки формата "WIDTHxHEIGHT"
     * Например: "1920x1080" => "16:9"
     */
    public function getAspectRatioFromResolution(string $resolution): ?string
    {
        if (!str_contains($resolution, 'x')) {
            return null;
        }

        [$width, $height] = explode('x', $resolution);

        $width = (int)$width;
        $height = (int)$height;

        if ($width === 0 || $height === 0) {
            return null;
        }

        $gcd = function (int $a, int $b) use (&$gcd): int {
            return $b === 0 ? $a : $gcd($b, $a % $b);
        };

        $gcdValue = $gcd($width, $height);

        return ($width / $gcdValue) . ':' . ($height / $gcdValue);
    }
}
