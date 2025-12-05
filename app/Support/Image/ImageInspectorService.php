<?php

namespace App\Support\Image;

use RuntimeException;

class ImageInspectorService
{
    /**
     * Получает разрешение изображения (ширина x высота).
     *
     * @throws RuntimeException
     */
    public function getResolution(string $path): ?string
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Image file not found: $path");
        }

        $size = getimagesize($path);

        if (!$size || !isset($size[0], $size[1])) {
            throw new RuntimeException("Failed to get image size for file: $path");
        }

        return $size[0] . 'x' . $size[1];
    }
}
