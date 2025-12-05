<?php

namespace App\DTO;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class ApiTokenCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $service,
        public readonly string $email,
        public readonly string $token,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            service: $data['service'],
            email: $data['email'],
            token: $data['token'],
        );
    }

    public static function fromRequest(Request $request): static
    {
        return static::fromArray($request->validated());
    }


    public function toArray(): array
    {
        return [
            'service' => $this->service,
            'email'   => $this->email,
            'token'   => $this->token,
        ];
    }
}
