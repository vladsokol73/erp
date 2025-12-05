<?php

namespace App\DTO\Creative;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TagDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string|null $style,
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Tag)) {
            throw new \InvalidArgumentException('Expected Tag type model');
        }

        return new self(
            id: $model->id,
            name: $model->name,
            style: $model->style
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
