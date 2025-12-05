<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TicketListDto extends BaseDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $status,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?array $client = null,
        public readonly ?array $operator = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'client' => $this->client,
            'operator' => $this->operator
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Ticket)) {
            throw new \InvalidArgumentException('Ожидалась модель типа Ticket');
        }

        return new self(
            id: $model->id,
            title: $model->title,
            status: $model->status,
            created_at: $model->created_at?->format('Y-m-d H:i:s'),
            updated_at: $model->updated_at?->format('Y-m-d H:i:s'),
            client: $model->client ? ['id' => $model->client->id, 'name' => $model->client->name] : null,
            operator: $model->operator ? ['id' => $model->operator->id, 'name' => $model->operator->name] : null
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($ticket) => static::fromModel($ticket))->toArray();
    }
}
