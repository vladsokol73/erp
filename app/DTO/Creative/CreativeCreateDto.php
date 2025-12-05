<?php

namespace App\DTO\Creative;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class CreativeCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $code,
        public readonly string $url,
        public readonly string $type,
        public readonly string $resolution,
        public readonly int $country_id,
        public readonly int $user_id,
        public readonly array $tags = [],
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            code: $request->input('code'),
            url: $request->input('url'),
            type: $request->input('type'),
            resolution: $request->input('resolution'),
            country_id: $request->input('country_id'),
            user_id: $request->user()->id,
            tags: $request->input('tags', []),
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'url' => $this->url,
            'type' => $this->type,
            'resolution' => $this->resolution,
            'country_id' => $this->country_id,
            'user_id' => $this->user_id,
            'tags' => $this->tags,
        ];
    }
}
