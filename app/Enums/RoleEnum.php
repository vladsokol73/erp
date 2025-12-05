<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case BUYER = 'buyer';
    case OPERATOR = 'operator';
}
