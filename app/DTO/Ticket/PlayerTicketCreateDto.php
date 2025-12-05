<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class PlayerTicketCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $player_id,
        public readonly string $type,
        public readonly int $tg_id,
        public readonly float $sum,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            user_id: $request->user()->id,
            player_id: $request->integer('player_id'),
            type: $request->string('type'),
            tg_id: $request->integer('tg_id'),
            sum: $request->float('sum'),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'player_id' => $this->player_id,
            'type' => $this->type,
            'tg_id' => $this->tg_id,
            'sum' => $this->sum,
        ];
    }
}


