<?php

namespace App\DTO;

use App\Contracts\DTOs\FromArrayInterface;
use App\Contracts\DTOs\ToArrayInterface;

class FileUploadDto implements FromArrayInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $url,
        public readonly string $code,
        public readonly string $type,
        public readonly ?string $resolution = null,
        public readonly ?string $ratio = null,
        public readonly ?string $poster = null,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self(
            $data['url'],
            $data['code'],
            $data['type'],
            $data['resolution'] ?? null,
            $data['ratio'] ?? null,
            $data['poster'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'code' => $this->code,
            'type' => $this->type,
            'resolution' => $this->resolution,
            'dimensions' => $this->ratio,
            'poster' => $this->poster,
        ];
    }
}
