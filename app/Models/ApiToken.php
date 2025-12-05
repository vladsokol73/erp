<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;

class ApiToken extends Model
{
    protected $fillable = [
        'service',
        'email',
        'token'
    ];

    protected function token(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Crypt::decryptString($value),
            set: fn ($value) => Crypt::encryptString($value),
        );
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('service', 'ILIKE', "%{$search}%")
                ->orWhere('email', 'ILIKE', "%{$search}%");
        });
    }

    public function scopeC2Desk($query)
    {
        return $query->where('service', 'Chat2Desk');
    }

    public function users(): EloquentBelongsToMany
    {
        return $this->belongsToMany(\App\Models\User\User::class, 'api_token_user', 'api_token_id', 'user_id');
    }
}
