<?php

namespace App\DTO\Operator;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\Operator\AiRetentionReport;
use App\Models\Operator\AiRetentionReportTest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AiRetentionReportDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public int $id,
        public int $operator_id,
        public int $client_id,
        public ?int $score = null,
        public string $comment = '',
        public string $analysis = '',
        public ?array $raw_payload = null,
        public ?string $conversation_date = null,
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof AiRetentionReport)) {
            throw new \InvalidArgumentException('Expected AiRetentionReport type model');
        }

        return new self(
            id: $model->id,
            operator_id: $model->operator_id,
            client_id: $model->client_id,
            score: $model->score,
            comment: $model->comment,
            analysis: $model->analysis,
            raw_payload: $model->raw_payload ?? null,
            conversation_date: $model->conversation_date?->toDateString(),
        );
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection
            ->map(fn($item) => static::fromModel($item))
            ->toArray();
    }
}
