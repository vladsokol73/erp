<?php
declare(strict_types=1);

namespace App\Contracts\TwoFactor;

use App\Models\User\User;

interface TwoFactorService
{
    /** Сгенерировать secret и QR (для текущего пользователя) */
    public function generateSecretWithQr(int $size = 200): array;

    /** Верифицировать код по произвольному secret */
    public function verifyCode(string $secret, string $code): bool;

    /** Включить 2FA пользователю (без проверки кода) */
    public function enableForUser(string $secret, ?User $user = null): void;

    /** Отключить 2FA пользователю */
    public function disableForUser(?User $user = null): void;

    /** Проверить код по secret пользователя (из профиля) */
    public function verifyForUser(string $code, ?User $user = null): bool;
}
