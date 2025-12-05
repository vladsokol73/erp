<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromRequestInterface;
use Illuminate\Http\Request;

class ChannelEditDto extends BaseDto implements FromRequestInterface
{
    public function __construct(
        public readonly string $name
    ) {
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name')
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name
        ];
    }
}
