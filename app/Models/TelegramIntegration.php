<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelegramIntegration extends Model
{
    use SoftDeletes;
    public const UPDATED_AT = null; // Отключаем updated_at

    protected $fillable = [
        'user_id',
        'key',
        'tg_id',
        'activated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
