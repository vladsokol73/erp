<?php

namespace App\Providers;

use App\Contracts\Qr\QrGenerator;
use App\Contracts\Security\ApiKeyValidator;
use App\Contracts\TwoFactor\TwoFactorService;
use App\Contracts\User\UserRegistrar;
use App\Contracts\TwoFactor\TwoFactorProvider;
use App\Contracts\Webhook\TelegramIntegrationManager;
use App\Services\GuardService;
use App\Services\Qr\SimpleQrGenerator;
use App\Services\TwoFactor\GoogleTwoFactorProvider;
use App\Services\Security\ConfigApiKeyValidator;
use App\Services\TwoFactor\GoogleTwoFactorService;
use App\Services\User\EloquentUserRegistrar;
use App\Services\Webhook\EloquentTelegramIntegrationManager;
use App\Services\User\PermissionService;
use App\Services\User\RoleService;
use App\Contracts\Security\JwtEncoder;
use App\Services\Security\GChatJwtService;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

//use Illuminate\Support\Facades\Blade;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('guard-service', fn ($app) => new GuardService(
            $app->make(PermissionService::class),
            $app->make(RoleService::class),
        ));

        // 2FA and QR bindings
        $this->app->bind(TwoFactorProvider::class, GoogleTwoFactorProvider::class);
        $this->app->bind(QrGenerator::class, SimpleQrGenerator::class);
        $this->app->bind(TwoFactorService::class, GoogleTwoFactorService::class);

        // API key and registration bindings
        $this->app->bind(ApiKeyValidator::class, ConfigApiKeyValidator::class);
        $this->app->bind(UserRegistrar::class, EloquentUserRegistrar::class);
        $this->app->bind(TelegramIntegrationManager::class, EloquentTelegramIntegrationManager::class);

        // Security
        $this->app->bind(JwtEncoder::class, GChatJwtService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

}
