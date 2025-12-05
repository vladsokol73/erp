<?php

namespace App\DTO;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CommentDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $comment,
        public readonly string $user_name,
        public readonly string $created_at
    ) {
    }

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Comment)) {
            throw new \InvalidArgumentException('Expected Comment type model');
        }

        return new self(
            id: $model->id,
            comment: $model->comment,
            user_name: $model->user()->withTrashed()->first()->name,
            created_at: $model->created_at?->format('Y-m-d H:i:s'),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
