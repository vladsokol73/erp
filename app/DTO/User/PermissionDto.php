<?php

namespace App\DTO\User;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\User\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PermissionDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $title,
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Permission)) {
            throw new \InvalidArgumentException('Expected Permission type model');
        }

        return new self(
            id: $model->id,
            name: $model->guard_name,
            title: $model->title
        );
    }

    public static function groupByPrefix(array $permissions): array
    {
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $prefix = $parts[0] ?? 'other';

            if (!isset($grouped[$prefix])) {
                $grouped[$prefix] = [];
            }

            $grouped[$prefix][] = $permission;
        }

        return $grouped;
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
