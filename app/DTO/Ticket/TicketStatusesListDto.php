<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketStatusesListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $color,
        public readonly bool $is_default,
        public readonly bool $is_final,
        public readonly bool $is_approval,
        public readonly ?int $sort_order,
        public readonly ?string $created_at,
    ) {}

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'color'      => $this->color,
            'is_default' => $this->is_default,
            'is_final'   => $this->is_final,
            'is_approval' => $this->is_approval,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
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
            is_approval: (bool) $model->is_approval,
            sort_order: $model->sort_order,
            created_at: optional($model->created_at)?->toDateTimeString(),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
