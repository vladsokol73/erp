<?php

namespace App\DTO;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProjectDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int       $id,
        public readonly string    $name,
        public readonly string    $description,
        public readonly string    $currency,
        public readonly int       $country_id,
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Project)) {
            throw new \InvalidArgumentException('Expected Like type model');
        }

        return new self(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            currency: $model->currency,
            country_id: $model->country_id
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
