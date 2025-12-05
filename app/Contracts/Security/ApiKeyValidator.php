<?php

namespace App\Contracts\Security;

interface ApiKeyValidator
{
    /**
     * Validate provided API key from a request header or any source.
     */
    public function isValid(?string $providedKey): bool;
}


