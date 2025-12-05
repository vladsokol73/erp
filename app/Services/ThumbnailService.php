<?php

namespace App\Services;

class ThumbnailService
{
    /**
     * Генерирует постер (thumbnail) из видеофайла с помощью ffmpeg
     * @param string $videoPath
     * @param string $code
     * @return string Путь к сгенерированному webp-файлу
     * @throws \RuntimeException
     */
    public function generateThumbnail(string $videoPath, string $code): string
    {
        $outputDir = sys_get_temp_dir();
        $outputPath = $outputDir . DIRECTORY_SEPARATOR . $code . '.webp';
        // ffmpeg: взять кадр на 1-й секунде, сохранить как webp
        $cmd = sprintf(
            'ffmpeg -y -i %s -ss 00:00:01.000 -vframes 1 -vf "scale=320:-1" %s',
            escapeshellarg($videoPath),
            escapeshellarg($outputPath)
        );
        exec($cmd, $output, $resultCode);
        if ($resultCode !== 0 || !file_exists($outputPath)) {
            throw new \RuntimeException('Не удалось сгенерировать постер для видео');
        }
        return $outputPath;
    }

    public function getUrl(string $code): string
    {
        return config("app.AWS_URL") . "/creo/video/thumbnails/{$code}.webp";
    }
}
