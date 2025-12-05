<?php

namespace App\Services\TwoFactor;

use App\Contracts\TwoFactor\TwoFactorProvider;
use PragmaRX\Google2FA\Google2FA;

class GoogleTwoFactorProvider implements TwoFactorProvider
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

    public function getQrCodeUrl(string $issuer, string $accountName, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl($issuer, $accountName, $secret);
    }

    public function verifyCode(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }
}


