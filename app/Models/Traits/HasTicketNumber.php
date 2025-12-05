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

        // Получаем первые буквы из слага топика
        $prefix = strtoupper(self::getInitials($topicSlug));

        // Текущая дата
        $date = now()->format('d/m/y');

        // Количество тикетов за сегодня + 1, дополняем нулями до 5 цифр
        $countToday = static::whereDate('created_at', now())->count() + 1;
        $ticketNumber = str_pad($countToday, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$ticketNumber}-{$date}";
    }

    protected static function getInitials(string $slug): string
    {
        // Разбиваем слаг по дефису, берем первые буквы каждого слова
        return implode('', array_map(fn($word) => mb_substr($word, 0, 1), explode('-', $slug)));
    }
}
