<?php

namespace App\DTO\Client;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Client\FailedJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FailedJobListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public int $id,                    // ID задания
        public string $connection,         // Имя соединения (например, redis)
        public string $queue,              // Имя очереди (например, default)
        public string $failed_at,          // Метка времени неудачи (в формате ISO)
        public string $exception           // Сообщение об ошибке
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof FailedJob)) {
            throw new \InvalidArgumentException('Expected FailedJob type model');
        }

        return new self(
            id: $model->id,
            connection: $model->connection,
            queue: $model->queue,
            failed_at: $model->failed_at->toISOString(),
            exception: $model->exception
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
