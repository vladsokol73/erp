<?php

namespace App\Models\Traits;

trait HasTicketNumber
{
    protected static function bootHasTicketNumber(): void
    {
        static::creating(function ($model) {
            if (empty($model->ticket_number)) {
                $model->ticket_number = static::generateTicketNumber($model);
            }
        });
    }

    protected static function generateTicketNumber($model): string
    {
        $topicSlug = optional($model->topic)->slug ?? 'unknown';
        $prefix = strtoupper(self::getInitials($topicSlug));

        // Формат даты для номера
        $date = now()->format('d-m-y');

        // Ищем последний номер за сегодня
        $lastTicket = static::whereDate('created_at', now())
            ->where('ticket_number', 'LIKE', "{$prefix}-%-{$date}")
            ->orderByDesc('ticket_number')
            ->first();

        if ($lastTicket) {
            // Извлекаем порядковый номер из строки
            preg_match('/^' . preg_quote($prefix, '/') . '-(\d+)-' . preg_quote($date, '/') . '$/', $lastTicket->ticket_number, $matches);
            $nextNumber = isset($matches[1]) ? ((int)$matches[1] + 1) : 1;
        } else {
            $nextNumber = 1;
        }

        // Дополняем нулями до 5 символов
        $ticketNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$ticketNumber}-{$date}";
    }

    protected static function getInitials(string $slug): string
    {
        return implode('', array_map(fn($word) => mb_substr($word, 0, 1), explode('-', $slug)));
    }
}
