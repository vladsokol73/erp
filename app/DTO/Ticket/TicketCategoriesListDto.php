<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Ticket\TicketCategory;
use App\Services\Ticket\TicketStatusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TicketCategoriesListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $is_active,
        #[LiteralTypeScriptType('Array<TicketStatusDto>')]
        public readonly array $statuses = [],
        public readonly ?string $description,
        public readonly ?int $sort_order,
        public readonly ?string $created_at,
    ) {}

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'is_active'   => $this->is_active,
            'statuses'    => $this->statuses,
            'description' => $this->description,
            'sort_order'  => $this->sort_order,
            'created_at'  => $this->created_at,
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof TicketCategory)) {
            throw new \InvalidArgumentException('Expected TicketCategory instance');
        }

        $ticketStatusService = app(TicketStatusService::class);

        return new self(
            id: $model->id,
            name: $model->name,
            slug: $model->slug,
            is_active: (bool) $model->is_active,
            statuses: $ticketStatusService->getStatuses($model),
            description: $model->description,
            sort_order: $model->sort_order,
            created_at: optional($model->created_at)?->toDateTimeString(),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
