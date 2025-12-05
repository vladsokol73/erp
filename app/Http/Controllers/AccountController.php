<?php

namespace App\Http\Controllers;

use App\DTO\User\UserProfileDto;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Account\AccountSettingsService;
use App\Services\Telegram\TelegramIntegrationService;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function __construct(
        private readonly TelegramIntegrationService $telegramService,
        private readonly AccountSettingsService $accountSettingsService
    ) {}

    public function showSettings(?User $user = null): Response
    {
        $user = $user ?? Auth::user();

        return Inertia::render('Account/Settings', [
            'user' => UserProfileDto::fromModel($user),
        ]);
    }

    public function resetPassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->accountSettingsService->changePassword(
                $request->current_password,
                $request->new_password
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

    public function getTgLink(): JsonResponse
    {
        $key = $this->telegramService->getOrCreateKeyForUser();

        // Формируем ссылку на бота из конфига
        $botUsername = config('services.telegram.bot_username');
        $link = "https://t.me/{$botUsername}?start={$key}";

        return ApiResponse::success([
            'link' => $link,
        ]);
    }

    public function destroyTelegram(): JsonResponse
    {
        $this->telegramService->destroyForUser();

        return ApiResponse::successMessage('Telegram notifications disconnected.');
    }

    public function checkTelegram(): JsonResponse
    {
        $success = $this->telegramService->checkTelegramIntegration();

        if($success){
            return ApiResponse::success();
        } else {
            return ApiResponse::notFound();
        }
    }
}

