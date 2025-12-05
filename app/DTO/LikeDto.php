<?php

namespace App\DTO;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class LikeDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly int $value,
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Like)) {
            throw new \InvalidArgumentException('Expected Like type model');
        }

        return new self(
            id: $model->id,
            value: $model->value,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
