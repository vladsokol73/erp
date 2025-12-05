<?php

namespace App\Contracts\User;

use App\Models\User\User;

interface UserRegistrar
{
    /**
     * Register a new user and return the created model.
     * The $attributes must contain: name, email, password
     */
    public function register(array $attributes): User;
}


