<?php

namespace App\Contracts\TwoFactor;

interface TwoFactorProvider
{
    /**
     * Generate a new shared secret for a user/device.
     */
    public function generateSecretKey(): string;

    /**
     * Build an otpauth URI for QR encoding.
     */
    public function getQrCodeUrl(string $issuer, string $accountName, string $secret): string;

    /**
     * Verify a one-time code against a secret.
     */
    public function verifyCode(string $secret, string $code): bool;
}


