<?php

namespace App\DTO;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;


#[TypeScript]
class CountryDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string|null $iso,
        public readonly string|null $img
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Country)) {
            throw new \InvalidArgumentException('Expected Country type model');
        }

        return new self(
            id: $model->id,
            name: $model->name,
            iso: $model->iso,
            img: $model->img
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
