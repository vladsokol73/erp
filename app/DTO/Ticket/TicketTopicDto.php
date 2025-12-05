<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\TicketTopic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketTopicDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly int $category_id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        #[LiteralTypeScriptType('TicketFormFieldDto[]')]
        public readonly ?array $fields,
        public readonly bool $is_active,
        public readonly int $sort_order,
    ) {}

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'category_id' => $this->category_id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'fields'      => $this->fields,
            'is_active'   => $this->is_active,
            'sort_order'  => $this->sort_order,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof TicketTopic)) {
            throw new \InvalidArgumentException('Expected TicketTopic model');
        }

        return new self(
            id: $model->id,
            category_id: $model->category_id,
            name: $model->name,
            slug: $model->slug,
            description: $model->description,
            fields: TicketFormFieldDto::fromCollection($model->formFields),
            is_active: (bool) $model->is_active,
            sort_order: $model->sort_order,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
