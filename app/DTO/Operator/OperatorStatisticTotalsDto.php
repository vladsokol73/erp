<?php

namespace App\DTO\Operator;

use App\Contracts\DTOs\ToArrayInterface;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OperatorStatisticTotalsDto implements ToArrayInterface
{
    public function __construct(
        public readonly int $all_clients,
        public readonly int $all_new_clients,
    ) {}

    public function toArray(): array
    {
        return [
            'all_clients' => $this->all_clients,
            'all_new_clients' => $this->all_new_clients,
        ];
    }
}
