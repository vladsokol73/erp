<?php

namespace App\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum ValidationRuleType: string
{
    case Email = 'email';
    case Url = 'url';
    case MaxLength = 'max_length';
    case MinLength = 'min_length';
    case MaxNumber = 'max_number';
    case MinNumber = 'min_number';
    case MinDate = 'min_date';
    case MaxDate = 'max_date';
    case FileType = 'file_type';
    case Contains = 'contains';
    case NotContains = 'not_contains';
}
