<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'available_countries',
        'timezone',
        'available_clients',
        'available_operators',
        'role',
        'google2fa_secret',
        'google2fa_enabled',
        'available_tags'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected $casts = [
        'available_tags' => 'array',
        'available_countries' => 'array',
        'available_clients' => 'array',
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

    public function activeTelegramIntegrations(): bool
    {
        if ($this->telegramIntegrations()->whereNotNull('activated_at')->count() > 0) {
            return true;
        } else {
            return false;
        }
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

            $q->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        });
    }
}
