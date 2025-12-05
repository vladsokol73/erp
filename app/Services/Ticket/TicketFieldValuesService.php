<?php

namespace App\Services\Ticket;

use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketFieldValue;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class TicketFieldValuesService
{
    public function __construct(
        protected TicketFileService   $fileService,
        protected TicketStatusService $statusService,
        protected TicketLogService    $logService,
        protected NotificationService $notificationService,
    )
    {
    }

    public function getTicketFieldValues(Ticket $ticket): Collection
    {
        return $ticket->fieldValues()->get();
    }

    public function updateTicketFieldValues(Ticket $ticket, array $fields): void
    {
        foreach ($fields as $key => $value) {
            if (!str_starts_with($key, 'field_')) {
                continue;
            }

            $fieldId = (int)substr($key, 6);

            // Обработка файлов
            if ($value instanceof UploadedFile) {
                $value = $this->fileService->uploadSingleFile($value, $ticket->ticket_number, $fieldId);
            } elseif (is_array($value) && isset($value[0]) && $value[0] instanceof UploadedFile) {
                $value = $this->fileService->uploadMultipleFiles($value, $ticket->ticket_number, $fieldId);
            }

            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            // Обновляем или создаём новое значение
            TicketFieldValue::query()->updateOrCreate(
                [
                    'ticket_id' => $ticket->id,
                    'field_id' => $fieldId,
                ],
                [
                    'value' => $value,
                ]
            );
        }
    }
}
