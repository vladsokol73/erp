<?php

namespace App\Services\Security;

use App\Contracts\Security\ApiKeyValidator;

class ConfigApiKeyValidator implements ApiKeyValidator
{
    public function isValid(?string $providedKey): bool
    {
        $validKey = config('app.api-key');
        return is_string($providedKey) && $providedKey === $validKey;
    }
}


