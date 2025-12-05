<?php

namespace App\DTO\Client;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Client\ClientsLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ClientLogDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public int $id,
        public string $webhook_event,
        public array $webhook_data,
        public ?string $task_status,
        public ?int $worker_id,
        public ?string $started_at,
        public ?string $finished_at,
        public ?string $result,
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof ClientsLog)) {
            throw new \InvalidArgumentException('Expected ClientsLog type model');
        }

        return new self(
            id: $model->id,
            webhook_event: $model->webhook_event,
            webhook_data: json_decode($model->webhook_data, true),
            task_status: $model->task_status,
            worker_id: $model->worker_id,
            started_at: optional($model->started_at)?->toDateTimeString(),
            finished_at: optional($model->finished_at)?->toDateTimeString(),
            result: $model->result,
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
