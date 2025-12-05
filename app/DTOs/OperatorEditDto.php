<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromRequestInterface;
use Illuminate\Http\Request;

class OperatorEditDto extends BaseDto implements FromRequestInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly ?array $channel_ids = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'channel_ids' => $this->channel_ids
        ];
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            channel_ids: $request->input('channel_ids', [])
        );
    }
}
