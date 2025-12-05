<?php

namespace App\Services\Ticket;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TicketFileService
{
    /**
     * Загрузить одиночный файл и вернуть его публичный URL.
     */
    public function uploadSingleFile(UploadedFile $file, string $ticketNumber, int $fieldId): string
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = "{$ticketNumber}_{$fieldId}.{$extension}";

        Storage::disk('s3')->putFileAs(
            'uploads/',
            $file,
            $fileName,
            [
                'visibility' => 'public',
                'ContentType' => $file->getMimeType(),
                'ContentDisposition' => 'attachment'
            ]
        );

        return rtrim(config('app.AWS_URL'), '/') . "/uploads/{$fileName}";
    }

    /**
     * Загрузить массив файлов и вернуть JSON со списком URL.
     */
    public function uploadMultipleFiles(array $files, string $ticketNumber, int $fieldId): string
    {
        $uploadedPaths = [];

        foreach ($files as $index => $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = "{$ticketNumber}_{$fieldId}_{$index}.{$extension}";

            Storage::disk('s3')->putFileAs(
                'uploads/',
                $file,
                $fileName,
                [
                    'visibility' => 'public',
                    'ContentType' => $file->getMimeType()
                ]
            );

            $uploadedPaths[] = rtrim(config('app.AWS_URL'), '/') . "/uploads/{$fileName}";
        }

        return json_encode($uploadedPaths);
    }
}
