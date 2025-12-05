<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TicketUpdateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly ?int $status_id = null,
        public readonly ?int $user_id = null,
        public readonly ?string $priority = null,
        public readonly ?string $result = null,
        public readonly ?array $fields = null,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            status_id: $request->status_id ?? null,
            user_id: $request->user_id ?? null,
            priority: $request->priority ?? null,
            result: $request->result ?? null,
            fields: $request->fields ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'status_id' => $this->status_id,
            'user_id' => $this->user_id,
            'priority' => $this->priority,
            'result' => $this->result,
            'fields' => $this->fields,
        ];
    }
}
