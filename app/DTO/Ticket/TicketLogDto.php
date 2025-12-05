<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\DTO\User\UserDto;
use App\Models\Ticket\TicketLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketLogDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly int $ticket_id,
        public readonly UserDto $user,
        public readonly string $action,
        public readonly string $old_values,
        public readonly string $new_values,
        public readonly string $created_at,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'user_id' => $this->user,
            'action' => $this->action,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'created_at' => $this->created_at,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof TicketLog)) {
            throw new \InvalidArgumentException('Expected Comment type model');
        }

        return new self(
            id: $model->id,
            ticket_id: $model->ticket_id,
            user: UserDto::fromModel($model->user),
            action: $model->action,
            old_values: $model->old_values,
            new_values: $model->new_values,
            created_at: $model->created_at
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
