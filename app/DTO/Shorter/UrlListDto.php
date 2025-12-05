<?php

namespace App\DTO\Shorter;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\ShortUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UrlListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $original_url,
        public readonly string $short_code,
        public readonly string $domain,
        public readonly string $created_at
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'original_url' => $this->original_url,
            'short_code' => $this->short_code,
            'domain' => $this->domain,
            'created_at' => $this->created_at
        ];
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof ShortUrl)) {
            throw new \InvalidArgumentException('Expected Url type model');
        }

        return new self(
            id: $model->id,
            original_url: $model->original_url,
            short_code: $model->short_code,
            domain: $model->domain,
            created_at: $model->created_at
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
