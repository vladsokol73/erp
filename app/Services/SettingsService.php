<?php

namespace App\Services;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected int $cacheTtl = 600; // кэш на 10 минут

    /**
     * Получить настройку по ключу
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", $this->cacheTtl, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->getValue() : $default;
        });
    }

    /**
     * Установить настройку
     */
    public function set(string $key, mixed $value, SettingType $type = SettingType::STRING, ?string $description = null): void
    {
        $jsonValue = match ($type) {
            SettingType::JSON => (array) $value,
            default           => ['value' => $value],
        };

        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value_json' => $jsonValue,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("setting:{$key}");
    }

    /**
     * Очистить весь кэш настроек
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}
