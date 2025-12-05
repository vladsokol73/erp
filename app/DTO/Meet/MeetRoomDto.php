<?php

namespace App\DTO\Meet;

use App\Contracts\DTOs\FromArrayInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MeetRoomDto implements FromArrayInterface, ToArrayInterface
{    public function __construct(
        public readonly string $room,
        public readonly int $created_at,
        public readonly ?int $ttl_remaining = null,
    ) {}
    
    public static function fromArray(array $data): static
    {
        return new self(
            room: (string)($data['room'] ?? ''),
            created_at: (int)($data['created_at'] ?? 0),
            ttl_remaining: isset($data['ttl_remaining']) ? (int)$data['ttl_remaining'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'room' => $this->room,
            'created_at' => $this->created_at,
            'ttl_remaining' => $this->ttl_remaining,
        ];
    }
}
