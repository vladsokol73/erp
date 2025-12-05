<?php

namespace App\Http\Controllers;

use App\Contracts\TwoFactor\TwoFactorService;
use App\Http\Requests\Account\ConfirmTwoFactorRequest;
use App\Http\Requests\Account\VerifyTwoFactorRequest;
use App\Http\Responses\ApiResponse;

class Google2FAController extends Controller
{
    private const SESSION_KEY_VERIFIED = '2fa_verified';

    public function __construct(
        private readonly TwoFactorService $twoFactorService
    ) {}

    public function generateSecret()
    {
        $data = $this->twoFactorService->generateSecretWithQr(200);

        return ApiResponse::success([
            'secret' => $data['secret'],
            'qrCode' => $data['qrCode'],
        ]);
    }

    public function confirm(ConfirmTwoFactorRequest $request)
    {
        $validated = $request->validated(); // ['secret', 'code']

        // 1) Проверяем код по присланному secret
        $isValid = $this->twoFactorService->verifyCode(
            $validated['secret'],
            $validated['code']
        );

        if (!$isValid) {
            return ApiResponse::validationMessage('Invalid confirmation code');
        }

        // 2) Включаем 2FA пользователю
        $this->twoFactorService->enableForUser($validated['secret']);

        return ApiResponse::successMessage('2FA enabled');
    }

    public function disable()
    {
        $this->twoFactorService->disableForUser();

        return ApiResponse::successMessage('2FA disabled');
    }

    public function verify(VerifyTwoFactorRequest $request)
    {
        $validated = $request->validated(); // ['code']

        // Проверяем код по секрету текущего пользователя
        $isValid = $this->twoFactorService->verifyForUser($validated['code']);

        if ($isValid) {
            $request->session()->put(self::SESSION_KEY_VERIFIED, true);
            return ApiResponse::successMessage('2FA verified');
        }

        return ApiResponse::validationMessage('Invalid confirmation code');
    }
}
