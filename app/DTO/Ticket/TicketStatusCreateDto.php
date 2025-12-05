<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TicketStatusCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $color,
        public readonly bool $is_default,
        public readonly bool $is_final,
        public readonly bool $is_approval,
        public readonly ?int $sort_order,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name'),
            color: $request->input('color'),
            is_default: (bool) $request->input('is_default', false),
            is_final: (bool) $request->input('is_final', false),
            is_approval: (bool) $request->input('is_approval', false),
            sort_order: $request->input('sort_order'),
        );
    }

    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'color'      => $this->color,
            'is_default' => $this->is_default,
            'is_final'   => $this->is_final,
            'is_approval' => $this->is_approval,
            'sort_order' => $this->sort_order,
        ];
    }
}
