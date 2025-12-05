<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\ForceChangePasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Account\AccountSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class ForcedPasswordController extends Controller
{
    public function __construct(
        private readonly AccountSettingsService $accountSettingsService
    ) {}

    /** Показать страницу смены пароля (Inertia) */
    public function show(): InertiaResponse
    {
        return Inertia::render('Auth/ForcePasswordChange', [
            'userEmail' => auth()->user()?->email,
        ]);
    }

    /** Обновить пароль и снять флаг */
    public function update(ForceChangePasswordRequest $request): JsonResponse
    {
        try {
            // смена пароля через сервис
            $this->accountSettingsService->forceChangePassword(
                $request->password,
            );

            // обновляем сессию
            $request->session()->regenerate();
            $request->session()->migrate(true);

            return ApiResponse::successMessage('Password has been changed successfully.');
        } catch (ValidationException $e) {
            return ApiResponse::validation($e->errors(), 'Validation failed.');
        } catch (\Throwable) {
            return ApiResponse::serverError('An error occurred while changing password.');
        }
    }
}
