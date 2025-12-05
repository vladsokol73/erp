<?php

namespace App\DTO\Ticket;

use App\Enums\PlayerTicketStatusEnum;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PlayerTicketStatusDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $color,
    ) {}

    public function toArray(): array
    {
        return [
            'name'  => $this->name,
            'color' => $this->color,
        ];
    }

    public static function fromEnum(PlayerTicketStatusEnum $status): self
    {
        return new self(
            name: $status->value,
            color: $status->color(),
        );
    }
}
