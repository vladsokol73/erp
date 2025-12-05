<?php

namespace App\DTO\Operator;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OperatorStatisticListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly int $operator_id,              // ID оператора
        public readonly int $new_client_chats,         // Новые клиенты
        public readonly int $total_clients,            // Всего клиентов
        public readonly int|null $inbox_messages,      // Входящие (если isSingleDay)
        public readonly int|null $outbox_messages,     // Исходящие (если isSingleDay)
        public readonly int $total_time,               // Общее время работы
        public readonly int $reg_count,                // Кол-во регистраций
        public readonly int $dep_count,                // Кол-во депозитов
        public readonly string|null $start_time,       // Время начала смены
        public readonly string|null $end_time,         // Время окончания смены
        public readonly string|null $operator_name,    // Название операции
        public readonly float | null $operator_score,    // Рейтинг
        public readonly int $fd = 0,                    // Кол-во FD тикетов (Approved)
        public readonly float $cr_dialog_to_fd = 0.0    // CR fd / total_clients (%)
    ) {}

    public static function fromModel(Model $model): static
    {
        $attributes = $model->toArray();

        return new self(
            id: $attributes['id'],
            operator_id: $attributes['operator_id'],
            new_client_chats: $attributes['new_client_chats'],
            total_clients: $attributes['total_clients'],
            inbox_messages: $attributes['inbox_messages'] ?? null,
            outbox_messages: $attributes['outbox_messages'] ?? null,
            total_time: $attributes['total_time'],
            reg_count: $attributes['reg_count'],
            dep_count: $attributes['dep_count'],
            start_time: $attributes['start_time'] ?? null,
            end_time: $attributes['end_time'] ?? null,
            operator_name: $attributes['operation_name'] ?? null,
            operator_score: $attributes['operator_score'] ?? null,
            fd: (int)($attributes['fd'] ?? 0),
            cr_dialog_to_fd: (float)($attributes['cr_dialog_to_fd'] ?? 0)
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection
            ->map(fn($item) => static::fromModel($item))
            ->toArray();
    }
}
