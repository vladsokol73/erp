<?php

namespace App\DTO\Ticket;

use App\Enums\ValidationRuleType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ValidationRuleDto
{
    public function __construct(
        public ValidationRuleType $type,
        public mixed              $value = null,
    )
    {
    }
}
