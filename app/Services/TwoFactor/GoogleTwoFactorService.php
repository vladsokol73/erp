<?php
// app/Services/TwoFactor/GoogleTwoFactorService.php
declare(strict_types=1);

namespace App\Services\TwoFactor;

use App\Contracts\Qr\QrGenerator;
use App\Contracts\TwoFactor\TwoFactorProvider;
use App\Contracts\TwoFactor\TwoFactorService;

use App\Models\User\User;
use Illuminate\Support\Facades\Auth;

final class GoogleTwoFactorService implements TwoFactorService
{
    public function __construct(
        private readonly TwoFactorProvider $twoFactorProvider,
        private readonly QrGenerator $qrGenerator,
    ) {}

    public function generateSecretWithQr(int $size = 200): array
    {
        $user = Auth::user();
        if (!$user) {
            throw new \RuntimeException('User is not authenticated.');
        }

        $secret = $this->twoFactorProvider->generateSecretKey();
        $qrCodeUrl = $this->twoFactorProvider->getQrCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        $qrCode = $this->qrGenerator->generate($qrCodeUrl, $size);

        return [
            'secret' => $secret,
            'qrCode' => $qrCode,
        ];
    }

    public function verifyCode(string $secret, string $code): bool
    {
        // Верификация кода по произвольному secret
        return (bool) $this->twoFactorProvider->verifyCode($secret, $code);
    }

    public function enableForUser(string $secret, ?User $user = null): void
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            throw new \RuntimeException('User is not authenticated.');
        }

        $user->update([
            'google2fa_secret'  => $secret,
            'google2fa_enabled' => true,
        ]);
    }

    public function disableForUser(?User $user = null): void
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            throw new \RuntimeException('User is not authenticated.');
        }

        $user->update([
            'google2fa_secret'  => null,
            'google2fa_enabled' => false,
        ]);
    }

    public function verifyForUser(string $code, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            throw new \RuntimeException('User is not authenticated.');
        }

        $secret = (string) ($user->google2fa_secret ?? '');

        // Если секрет не задан — 2FA не настроена
        if ($secret === '') {
            return false;
        }

        return $this->verifyCode($secret, $code);
    }
}
