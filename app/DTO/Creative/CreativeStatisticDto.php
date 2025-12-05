<?php

namespace App\DTO\Creative;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\Models\CreativeStatistic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CreativeStatisticDto implements FromModelInterface, FromCollectionInterface
{
public function __construct(
    public readonly string $code,
    public readonly int $clicks,
    public readonly float $ctr,
    public readonly int $leads,
    public readonly string $date
) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof CreativeStatistic)) {
            throw new \InvalidArgumentException('Expected CreativeStatistic instance');
        }

        return new self(
            code: $model->code,
            clicks: $model->clicks,
            ctr: $model->ctr,
            leads: $model->leads,
            date: $model->date
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'clicks' => $this->clicks,
            'ctr' => $this->ctr,
            'leads' => $this->leads,
            'date' => $this->date
        ];
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}
