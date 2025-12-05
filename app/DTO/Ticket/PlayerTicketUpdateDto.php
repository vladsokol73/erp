<?php

namespace App\DTO\Ticket;

use Illuminate\Http\Request;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PlayerTicketUpdateDto
{
    public function __construct(
        public readonly string $status,
        public readonly string|null $result  = null,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            status: $request->status,
            result: $request->result ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'result' => $this->result,
        ];
    }
}
