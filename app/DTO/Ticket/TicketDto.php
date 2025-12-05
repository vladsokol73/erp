<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $ticket_number,
        public readonly int $topic_id,
        public readonly int $user_id,
        public readonly string $priority,
        public readonly string|null $result = null,
        public readonly string|null $approved_at = null,
        public readonly string|null $closed_at = null,
        public readonly string $created_at,
        public readonly int $status_id
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Ticket)) {
            throw new \InvalidArgumentException('Expected TicketCategory instance');
        }

        return new self(
            id: $model->id,
            ticket_number: $model->ticket_number,
            topic_id: $model->topic_id,
            user_id: $model->user_id,
            priority: $model->priority,
            result: $model->result,
            approved_at: $model->approved_at?->format('Y-m-d H:i:s'),
            closed_at: $model->closed_at?->format('Y-m-d H:i:s'),
            created_at: $model->created_at->format('Y-m-d H:i:s'),
            status_id: $model->status_id
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'ticket_number' => $this->ticket_number,
            'topic_id'      => $this->topic_id,
            'user_id'       => $this->user_id,
            'priority'      => $this->priority,
            'result'        => $this->result,
            'approved_at'   => $this->approved_at,
            'closed_at'     => $this->closed_at,
            'created_at'    => $this->created_at,
            'status_id'     => $this->status_id
        ];
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
