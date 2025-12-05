<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TicketLogCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly int $ticket_id,
        public readonly int $user_id,
        public readonly string $action,
        public readonly string $old_values,
        public readonly string $new_values,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            ticket_id: $request->input('ticket_id'),
            user_id: $request->user()->id,
            action: $request->input('action'),
            old_values: $request->input('old_values'),
            new_values: $request->input('new_values'),
        );
    }

    public function toArray(): array
    {
        return [
            'ticket_id' => $this->ticket_id,
            'user_id' => $this->user_id,
            'action' => $this->action,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
        ];
    }
}
