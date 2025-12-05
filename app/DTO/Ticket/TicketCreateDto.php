<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TicketCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $category_id,
        public readonly int $topic_id,
        public readonly string $priority,
        public readonly array $fields,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            user_id: $request->input('user_id'),
            category_id: $request->integer('category_id'),
            topic_id: $request->integer('topic_id'),
            priority: $request->string('priority'),
            fields: $request->input('fields', []),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id'     => $this->user_id,
            'category_id' => $this->category_id,
            'topic_id'    => $this->topic_id,
            'priority'    => $this->priority,
            'fields'      => $this->fields,
        ];
    }
}
