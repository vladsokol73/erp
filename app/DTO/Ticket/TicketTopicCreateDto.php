<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TicketTopicCreateDto implements FromRequestInterface, ToArrayInterface
{
    /**
     * @param TicketResponsibleUserDto[] $responsible
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $category_id,
        public readonly int $sort_order,
        public readonly bool $is_active,
        public readonly array $approval = [],
        public readonly array $responsible = [],
        public readonly array $fields = [],
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            category_id: $request->input('category_id'),
            sort_order: $request->input('sort_order', 0),
            is_active: (bool) $request->input('is_active', false),

            approval: collect($request->input('approval', []))
                ->filter(fn($item) => is_array($item))
                ->map(fn(array $item) => TicketResponsibleUserDto::fromArray($item))
                ->all(),

            responsible: collect($request->input('responsible', []))
                ->filter(fn($item) => is_array($item))
                ->map(fn(array $item) => TicketResponsibleUserDto::fromArray($item))
                ->all(),

            fields: collect($request->input('fields', []))
                ->filter(fn($item) => is_array($item))
                ->map(fn(array $item) => TicketFormFieldDto::fromArray($item))
                ->all(),
        );
    }

    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
            'approval'    => $this->approval->toArray(),
            'responsible' => array_map(
                fn(TicketResponsibleUserDto $dto) => $dto->toArray(),
                $this->responsible
            ),
        ];
    }
}
