<?php

namespace App\Contracts\Qr;

interface QrGenerator
{
    /**
     * Generate a QR code (SVG or data URI) for a given string payload.
     */
    public function generate(string $payload, int $size = 200): string;
}


