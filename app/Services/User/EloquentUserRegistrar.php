<?php

namespace App\Services\User;

use App\Contracts\User\UserRegistrar;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;

class EloquentUserRegistrar implements UserRegistrar
{
    public function register(array $attributes): User
    {
        $name = $attributes['name'] ?? '';
        $email = $attributes['email'] ?? '';
        $password = $attributes['password'] ?? '';

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'timezone' => 0,
            'google_2fa_enabled' => false,
        ]);

        $user->roles()->attach(3);

        return $user;
    }
}


