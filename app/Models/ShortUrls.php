<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortUrls extends Model
{
    protected $fillable = [
        'original_url',
        'short_code',
        'domain',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\User::class);
    }
}
