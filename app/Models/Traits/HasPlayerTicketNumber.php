<?php

namespace App\Models\Traits;

trait HasPlayerTicketNumber
{
    protected static function bootHasPlayerTicketNumber(): void
    {
        static::creating(function ($model) {
            if (empty($model->ticket_number)) {
                $model->ticket_number = static::generatePlayerTicketNumber();
            }
        });
    }

    protected static function generatePlayerTicketNumber(): string
    {
        $prefix = 'PT';

        // Формат даты для номера
        $date = now()->format('d-m-y');

        // Ищем последний номер за сегодня с нужным префиксом и датой
        $lastTicket = static::whereDate('created_at', now())
            ->where('ticket_number', 'LIKE', "{$prefix}-{$date}-%")
            ->orderByDesc('ticket_number')
            ->first();

        if ($lastTicket) {
            // Извлекаем порядковый номер из строки
            preg_match('/^' . preg_quote($prefix, '/') . '-' . preg_quote($date, '/') . '-(\d+)$/', $lastTicket->ticket_number, $matches);
            $nextNumber = isset($matches[1]) ? ((int)$matches[1] + 1) : 1;
        } else {
            $nextNumber = 1;
        }

        // Дополняем нулями до 5 символов
        $ticketNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$ticketNumber}";
    }
}


