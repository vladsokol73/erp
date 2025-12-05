<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromRequestInterface;
use Illuminate\Http\Request;

class TicketEditDto extends BaseDto implements FromRequestInterface
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ?int $status_id = null,
        public readonly ?int $category_id = null,
        public readonly ?int $topic_id = null,
        public readonly ?array $responsible_users = []
    ) {
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            title: $request->input('title'),
            description: $request->input('description'),
            status_id: $request->input('status_id'),
            category_id: $request->input('category_id'),
            topic_id: $request->input('topic_id'),
            responsible_users: $request->input('responsible_users', [])
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'status_id' => $this->status_id,
            'category_id' => $this->category_id,
            'topic_id' => $this->topic_id,
            'responsible_users' => $this->responsible_users
        ];
    }
}
