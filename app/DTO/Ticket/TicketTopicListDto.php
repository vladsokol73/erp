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
class TicketTopicListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        #[LiteralTypeScriptType('TicketCategoryDto')]
        public readonly TicketCategoryDto $category,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        #[LiteralTypeScriptType('TicketResponsibleUserDto[]')]
        public readonly array $approval,
        #[LiteralTypeScriptType('TicketResponsibleUserDto[]')]
        public readonly array $responsible,
        #[LiteralTypeScriptType('TicketFormFieldDto[]')]
        public readonly array $fields,
        public readonly bool $is_active,
        public readonly int $sort_order,
        public readonly ?string $created_at,
    ) {}

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'category_id' => $this->category,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'approval'    => $this->approval,
            'responsible' => $this->responsible,
            'fields'      => $this->fields,
            'is_active'   => $this->is_active,
            'sort_order'  => $this->sort_order,
            'created_at'  => $this->created_at,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof TicketTopic)) {
            throw new \InvalidArgumentException('Expected TicketTopic model');
        }

        return new self(
            id: $model->id,
            category: TicketCategoryDto::fromModel($model->category),
            name: $model->name,
            slug: $model->slug,
            description: $model->description,
            approval: TicketResponsibleUserDto::fromCollection($model->approvalUsers),
            responsible: TicketResponsibleUserDto::fromCollection($model->responsibleUsers),
            fields: TicketFormFieldDto::fromCollection($model->formFields),
            is_active: (bool) $model->is_active,
            sort_order: $model->sort_order,
            created_at: optional($model->created_at)?->toDateTimeString(),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
