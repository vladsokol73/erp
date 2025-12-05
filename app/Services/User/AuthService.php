<?php

namespace App\Services\User;

use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class AuthService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function attemptLogin(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Incorrect username or password'
            ];
        }

        $user = Auth::user();

        // Проверяем двухфакторную аутентификацию
        if ($user->google2fa_enabled) {
            Auth::logout();

            // Сохраняем ID пользователя в сессии
            session(['2fa_user_id' => $user->id]);

            return [
                'success' => false,
                'requires2fa' => true
            ];
        }

        return [
            'success' => true,
            'user' => $user
        ];
    }

    public function verify2FACode(string $code): array
    {
        $userId = session('2fa_user_id');

        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Invalid session'
            ];
        }

        $user = User::find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        try {
            $valid = $this->google2fa->verifyKey($user->google2fa_secret, $code);

            if ($valid) {
                // Очищаем сессию
                session()->forget('2fa_user_id');

                // Авторизуем пользователя
                Auth::login($user);

                return [
                    'success' => true,
                    'user' => $user
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid verification code'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred during verification'
            ];
        }
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
