<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TicketCategoryCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $name,
        public readonly bool $is_active,
        public readonly ?string $description,
        public readonly ?int $sort_order,
        public readonly array $statuses,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name'),
            is_active: (bool) $request->input('is_active'),
            description: $request->input('description'),
            sort_order: $request->input('sort_order'),
            statuses: $request->input('statuses', []),
        );
    }

    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'is_active'   => $this->is_active,
            'description' => $this->description,
            'sort_order'  => $this->sort_order,
            'statuses'    => $this->statuses,
        ];
    }
}
