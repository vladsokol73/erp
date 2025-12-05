<?php

namespace App\Services\Ticket;

use App\Models\Ticket\PlayerTicket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PlayerTicketFileService
{
    /**
     * Загрузить скрин для PlayerTicket и вернуть публичный URL.
     */
    public function uploadScreenshot(UploadedFile $file, PlayerTicket $ticket): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = "screen.{$extension}";
        $directory = 'player_tickets/' . $ticket->ticket_number . '/';

        Storage::disk('s3')->putFileAs(
            $directory,
            $file,
            $fileName,
            [
                'visibility' => 'public',
                'ContentType' => $file->getMimeType(),
                'ContentDisposition' => 'attachment'
            ]
        );

        return rtrim(config('app.AWS_URL'), '/') . "/{$directory}{$fileName}";
    }

    // URL-источник более не поддерживается: загрузка только через файл из формы

    /**
     * Загрузить массив вложений и вернуть JSON со списком публичных URL.
     */
    public function uploadMultipleAttachments(array $files, PlayerTicket $ticket): string
    {
        $uploadedPaths = [];
        $directory = 'player_tickets/' . $ticket->ticket_number . '/attachments/';

        foreach ($files as $index => $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = "{$index}.{$extension}";

            Storage::disk('s3')->putFileAs(
                $directory,
                $file,
                $fileName,
                [
                    'visibility' => 'public',
                    'ContentType' => $file->getMimeType(),
                    'ContentDisposition' => 'attachment'
                ]
            );

            $uploadedPaths[] = rtrim(config('app.AWS_URL'), '/') . "/{$directory}{$fileName}";
        }

        return json_encode($uploadedPaths);
    }

    /**
     * Удалить файл по публичному URL с S3.
     */
    public function deleteFileByUrl(string $url): bool
    {
        $staticPrefix = rtrim(config('app.AWS_URL'), '/') . '/';
        $path = str_replace($staticPrefix, '', $url);
        if (Storage::disk('s3')->exists($path)) {
            Storage::disk('s3')->delete($path);
            return true;
        }
        return false;
    }

    /**
     * Удалить несколько файлов по массиву URL (или JSON-строке с URL).
     * Возвращает количество успешно удалённых файлов.
     */
    public function deleteManyByUrls(array|string $urls): int
    {
        if (is_string($urls)) {
            $decoded = json_decode($urls, true);
            $urls = is_array($decoded) ? $decoded : [];
        }

        $deleted = 0;
        foreach ($urls as $url) {
            if (is_string($url) && $this->deleteFileByUrl($url)) {
                $deleted++;
            }
        }

        return $deleted;
    }
}


