<?php

namespace App\DTO\Shorter;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class UrlCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $original_url,
        public readonly string $domain,
        public readonly string|null $short_code = null,
        public readonly int|null $id = null,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            original_url: $request->get('original_url'),
            domain: $request->get('domain'),
            short_code: $request->get('short_code'),
            id: $request->get('id'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'original_url' => $this->original_url,
            'short_code' => $this->short_code,
            'domain' => $this->domain,
        ];
    }
}
