<?php

namespace App\Services\Qr;

use App\Contracts\Qr\QrGenerator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SimpleQrGenerator implements QrGenerator
{
    public function generate(string $payload, int $size = 200): string
    {
        return QrCode::size($size)->generate($payload);
    }
}


