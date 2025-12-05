<?php

namespace App\DTO;

use App\Contracts\DTOs\FromModelInterface;
use App\Contracts\DTOs\FromCollectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Notifications\DatabaseNotification;

class NotificationDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $message,
        public readonly string $created_at,
        public readonly bool $unread,
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof DatabaseNotification)) {
            throw new \InvalidArgumentException('Expected DatabaseNotification model');
        }

        return new self(
            id: $model->id,
            message: $model->data['message'] ?? 'No message',
            created_at: $model->created_at,
            unread: is_null($model->read_at),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
