<?php

namespace App\DTO;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\ApiToken;
use App\Support\StringMasker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ApiTokenDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $service,
        public readonly string $email,
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof ApiToken)) {
            throw new \InvalidArgumentException('Expected ApiToken type model');
        }

        return new self(
            id: $model->id,
            service: $model->service,
            email: $model->email,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
