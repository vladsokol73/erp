<?php

namespace App\DTO\Creative;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class TagCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $style,
    ){}

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name'),
            style: $request->input('style'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'style' => $this->style,
        ];
    }
}
