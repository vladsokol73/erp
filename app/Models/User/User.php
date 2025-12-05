<?php

namespace App\Models\User;

use App\Models\Comment;
use App\Models\Creative;
use App\Models\Favorite;
use App\Models\Scopes\HasCreativesWithCountryFilter;
use App\Models\Scopes\HasCreativesWithTagFilter;
use App\Models\ShortUrl;
use App\Models\TelegramIntegration;
use App\Models\Traits\HasFlags;
use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasRolesAndPermissions, SoftDeletes, Notifiable;
    use HasCreativesWithCountryFilter;
    use HasCreativesWithTagFilter;
    use HasFlags;

    protected $fillable = [
        'name',
        'email',
        'password',
        'available_countries',
        'timezone',
        'available_channels',
        'available_operators',
        'role',
        'google2fa_secret',
        'google2fa_enabled',
        'available_tags',
        'operator_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected $casts = [
        'available_tags' => 'array',
        'available_countries' => 'array',
        'available_channels' => 'array',
        'available_operators' => 'array',
        'last_login_at' => 'date',
        'google2fa_enabled' => 'boolean',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function initial(): string
    {
        return preg_replace('/\b(\w)\w*\b/u', '$1', $this->name);
    }

    public function creatives(): HasMany
    {
        return $this->hasMany(Creative::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function shortUrls(): HasMany
    {
        return $this->hasMany(ShortUrl::class);
    }

    public function getShortName(): string {
        return preg_replace('~\h\K(\p{Lu})\p{Ll}+~u', '$1.', $this->name);
    }

    public function hasCountry($country = null): bool
    {
        return is_array($this->available_countries) && in_array($country, $this->available_countries);
    }

    // Проверка на наличие клиента или null
    public function hasClient($client = null): bool
    {
        return is_array($this->available_clients) && in_array($client, $this->available_clients);
    }

    // Проверка на наличие оператора или null
    public function hasOperator(string $operator = null)
    {
        return is_array($this->available_operators) && in_array($operator, $this->available_operators);
    }

    public function telegramIntegrations(): HasMany
    {
        return $this->hasMany(TelegramIntegration::class);
    }

    public function activeTelegramIntegrations(): bool
    {
        if ($this->telegramIntegrations()->whereNotNull('activated_at')->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function hasActiveTelegramIntegration(): bool
    {
        return $this->telegramIntegrations()
            ->whereNotNull('activated_at')
            ->exists();
    }

    public function favoriteCreatives(): HasManyThrough
    {
        return $this->hasManyThrough(
            Creative::class,
            Favorite::class,
            'user_id', // Foreign key on favorites table
            'id',      // Foreign key on creatives table
            'id',      // Local key on users table
            'source_id' // Local key on favorites table
        )->where('favorites.source', 'creatives');
    }

    public function apiTokens(): EloquentBelongsToMany
    {
        return $this->belongsToMany(\App\Models\ApiToken::class, 'api_token_user', 'user_id', 'api_token_id');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            if (is_numeric($search)) {
                $q->where('id', $search);
            }

            $q->orWhere('name', 'ILIKE', "%{$search}%")
                ->orWhere('email', 'ILIKE', "%{$search}%");
        });
    }

    public function scopeWithRole(Builder $query, string $role): Builder
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('title', $role);
        });
    }
}
