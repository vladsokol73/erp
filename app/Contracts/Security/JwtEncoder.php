<?php

namespace App\Contracts\Security;

interface JwtEncoder
{
    /**
     * Encode payload into a signed JWT string using HS256.
     *
     * @param array $payload
     * @return string
     */
    public function encode(array $payload): string;
}


