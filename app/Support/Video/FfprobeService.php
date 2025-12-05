<?php

namespace App\Support\Video;

use App\Models\Log;
use Exception;
use RuntimeException;

class FfprobeService
{
    /**
     * Получить разрешение видео (формат: ширина x высота).
     *
     */
    public function getResolution(string $path): ?string
    {
        if (!file_exists($path)) {
            throw new RuntimeException("File not found: $path");
        }

        // Подключаем библиотеки перед выполнением ffprobe
        $this->setFfmpegLibraryPath();

        $cmd = sprintf(
            'ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of json %s 2>&1',
            escapeshellarg($path)
        );

        $output = [];
        $resultCode = null;
        exec($cmd, $output, $resultCode);

        if ($resultCode !== 0 || empty($output[0])) {
            throw new RuntimeException("Failed to extract video resolution using ffprobe for file: $path");
        }
        // Склеиваем массив строк в одну JSON-строку
        $json = implode('', $output);

        // Превращаем JSON в массив
        $data = json_decode($json, true);

        // Проверяем наличие нужных данных
        if (!isset($data['streams'][0]['width'], $data['streams'][0]['height'])) {
            throw new RuntimeException("Width or height not found in ffprobe output for file: $path");
        }

        $width = $data['streams'][0]['width'];
        $height = $data['streams'][0]['height'];

        return $width . 'x' . $height;
    }


    /**
     * Генерировать thumbnail из видео и сохранить в указанное место.
     *
     */
    public function generateThumbnail(string $inputPath, string $outputPath): bool
    {
        $this->setFfmpegLibraryPath();
        if (!file_exists($inputPath)) {
            throw new RuntimeException("Input video file not found: $inputPath");
        }

        $cmd = sprintf(
            'ffmpeg -nostdin -i %s -vframes 1 -an -ss 1 -y -c:v libwebp -quality 80 %s 2>&1',
            escapeshellarg($inputPath),
            escapeshellarg($outputPath)
        );

        Log::info("Executing ffmpeg command: $cmd");

        $output = [];
        $resultCode = null;
        exec($cmd, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($outputPath)) {
            throw new RuntimeException("Failed to generate thumbnail for file: $inputPath");
        }

        return true;
    }


    /**
     * Устанавливает расширенный путь к библиотекам для FFmpeg
     */
    private function setFfmpegLibraryPath(): void
    {
        // Пути к библиотекам FFmpeg/FFprobe
        $libraryPaths = [
            '/layers/digitalocean_apt/apt/usr/lib/x86_64-linux-gnu/blas',  // Путь к libblas.so.3
            '/layers/digitalocean_apt/apt/usr/lib/x86_64-linux-gnu/lapack',  // Путь к liblapack.so.3
            '/layers/digitalocean_apt/apt/usr/lib/x86_64-linux-gnu/pulseaudio',
            dirname('/layers/digitalocean_apt/apt/usr/lib/x86_64-linux-gnu/blas/libblas.so.3'),  // Точный путь к libblas.so.3
            '/layers/digitalocean_apt/apt/usr/lib/x86_64-linux-gnu',
            '/layers/digitalocean_apt/apt/usr/lib',
            '/layers/digitalocean_apt/apt/lib/x86_64-linux-gnu',
            '/layers/digitalocean_apt/apt/lib',
            '/usr/local/lib',
            '/usr/lib/x86_64-linux-gnu',
            '/usr/lib',
            '/lib/x86_64-linux-gnu',
            '/lib'
        ];

        // Добавляем существующий системный LD_LIBRARY_PATH в начало массива
        $currentPath = getenv('LD_LIBRARY_PATH') ?: '';
        if ($currentPath) {
            array_unshift($libraryPaths, $currentPath);
        }
        // Составляем новый путь без дублирования
        $newPath = implode(':', array_unique($libraryPaths));
        putenv("LD_LIBRARY_PATH={$newPath}");
    }
}
