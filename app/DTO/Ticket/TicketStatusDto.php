<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketStatusDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $color,
        public readonly bool $is_default,
        public readonly bool $is_final,
        public readonly bool $is_approval,
    ) {}

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'color'      => $this->color,
            'is_default' => $this->is_default,
            'is_final'   => $this->is_final,
            'is_approval' => $this->is_approval,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof TicketStatus)) {
            throw new \InvalidArgumentException('Expected TicketStatus model');
        }

        return new self(
            id: $model->id,
            name: $model->name,
            slug: $model->slug,
            color: $model->color,
            is_default: (bool) $model->is_default,
            is_final: (bool) $model->is_final,
            is_approval: (bool) $model->is_approval
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
