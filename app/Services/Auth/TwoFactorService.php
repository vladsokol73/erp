<?php

namespace App\Services\Auth;

use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorService
{
    private Google2FA $google2fa;

    public function __construct(?Google2FA $google2fa = null)
    {
        $this->google2fa = $google2fa ?? new Google2FA();
    }

    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQrCodeSvg(string $issuer, string $accountName, string $secret, int $size = 200): string
    {
        $qrUrl = $this->google2fa->getQRCodeUrl($issuer, $accountName, $secret);
        return QrCode::size($size)->generate($qrUrl);
    }

    public function verifyCode(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }
}



