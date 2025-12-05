<?php

namespace App\Services\Creative;

use App\DTO\FileUploadDto;
use App\Models\Log;
use App\Services\ThumbnailService;
use App\Support\Image\ImageInspectorService;
use App\Support\MediaDimensionService;
use App\Support\Video\FfprobeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreativeFileService
{
    public function __construct(
        public readonly ThumbnailService        $thumbnailService,
        public readonly FfprobeService          $ffprobeService,
        public readonly ImageInspectorService   $imageMetadataService,
        public readonly MediaDimensionService   $mediaDimensionService
    ) {}

    /**
     * Загрузка файла (изображения или видео) на S3
     * @param UploadedFile $file
     * @param string $type
     * @return FileUploadDTO
     * @throws \Exception
     */
    public function uploadCreativeFile(UploadedFile $file, string $type): FileUploadDTO
    {
        try {


            // Генерация кода
            $uuid = Str::uuid();
            $shortUuid = substr($uuid->toString(), 0, 6);
            $code = $shortUuid;

            // Используем оригинальное расширение, но в нижнем регистре
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = $code . '.' . $extension;

            // Путь для S3
            $path = ($type === 'video' ? 'creo/video/' : 'creo/img/') . $fileName;

            // Загрузка файла
            $isUploaded = Storage::disk('s3')->put($path, file_get_contents($file), [
                'visibility' => 'public',
                'ContentType' => $file->getMimeType(),
                'ContentDisposition' => 'attachment'
            ]);

            if($isUploaded === false) {
                throw new \Exception('File upload s3 error.');
            }

            $url = Storage::disk('s3')->url($path);

            // Инициализация
            $resolution = null;
            $ratio = null;
            $poster = null;

            try {
                $realPath = $file->getRealPath();

                if ($type === 'image') {
                    $resolution = $this->imageMetadataService->getResolution($realPath);
                }

                if ($type === 'video') {
                    // Генерация превью
                    $posterLocalPath = tempnam(sys_get_temp_dir(), 'poster_') . '.webp';

                    $this->ffprobeService->generateThumbnail($realPath, $posterLocalPath);

                    $resolution = $this->ffprobeService->getResolution($realPath);

                    $thumbnailS3Path = "creo/video/thumbnails/{$code}.webp";
                    Storage::disk('s3')->put($thumbnailS3Path, file_get_contents($posterLocalPath), [
                        'visibility' => 'public',
                        'ContentType' => $file->getMimeType(),
                        'ContentDisposition' => 'attachment'
                    ]);
                    $poster = Storage::disk('s3')->url($thumbnailS3Path);
                }

                if ($resolution) {
                    $ratio = $this->mediaDimensionService->getAspectRatioFromResolution($resolution);
                }

            } catch (\Throwable $e) {
                throw new \RuntimeException("Failed to process media file: " . $e->getMessage(), previous: $e);
            }

            return new FileUploadDTO(
                url: $url,
                code: $code,
                type: $type,
                resolution: $ratio,
                poster: $poster
            );
        }catch (\Throwable $e) {
            throw new \RuntimeException("Failed to process media file: " . $e->getMessage(), previous: $e);
        }
    }


    /**
     * Удаление файла с S3 (и постера для видео)
     * @param string $url
     * @return bool
     */
    public function deleteCreativeFile(string $url): bool
    {
        $staticPrefix = config('app.AWS_URL') . '/';
        $path = str_replace($staticPrefix, '', $url);
        if (Storage::disk('s3')->exists($path)) {
            Storage::disk('s3')->delete($path);
            // Если это видео — удалить и постер
            if (str_contains($path, 'creo/video/')) {
                $filename = basename($path);
                $thumbnailPath = 'creo/video/thumbnails/' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                if (Storage::disk('s3')->exists($thumbnailPath)) {
                    Storage::disk('s3')->delete($thumbnailPath);
                }
            }
            return true;
        }
        return false;
    }
}
