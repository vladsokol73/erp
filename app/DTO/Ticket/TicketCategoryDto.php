<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\TicketCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketCategoryDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $is_active,
    ) {}

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'is_active'  => $this->is_active,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof TicketCategory)) {
            throw new \InvalidArgumentException('Expected TicketCategory instance');
        }

        return new self(
            id: $model->id,
            name: $model->name,
            slug: $model->slug,
            is_active: (bool) $model->is_active,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
