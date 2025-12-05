<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TicketFieldValueCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly int $ticket_id,
        public readonly int $field_id,
        public readonly string $value,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            ticket_id: $request->input('ticket_id'),
            field_id: $request->input('field_id'),
            value: $request->input('value'),
        );
    }

    public function toArray(): array
    {
        return [
            'ticket_id'  => $this->ticket_id,
            'field_id'   => $this->field_id,
            'value'      => $this->value,
        ];
    }
}
