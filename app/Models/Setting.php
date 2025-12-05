<?php

namespace App\Models;

use App\Enums\SettingType;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value_json',
        'type',
        'description',
    ];

    protected $casts = [
        'value_json' => 'array', // автоматическое преобразование JSON в массив
        'type' => SettingType::class,
    ];

    /**
     * Получить значение в приведённом виде в зависимости от типа.
     */
    public function getValue(): mixed
    {
        return match ($this->type) {
            SettingType::INTEGER => (int) ($this->value_json['value'] ?? 0),
            SettingType::BOOLEAN => (bool) ($this->value_json['value'] ?? false),
            SettingType::JSON    => $this->value_json,
            default              => (string) ($this->value_json['value'] ?? ''),
        };
    }
}
