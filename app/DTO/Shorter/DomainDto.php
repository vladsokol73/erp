<?php

namespace App\DTO\Shorter;

use App\Contracts\DTOs\FromArrayInterface;
use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class DomainDto implements FromArrayInterface, ToArrayInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $domain,
        public readonly string $redirect_url,
        public readonly string $created_at,
        public readonly bool $is_active
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
            domain: $data['domain'],
            redirect_url: $data['redirect_url'],
            created_at: $data['created_at'],
            is_active: $data['is_active'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'redirect_url' => $this->redirect_url,
            'created_at' => $this->created_at,
            'is_active' => $this->is_active,
        ];
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromArray($item))->toArray();
    }
}
