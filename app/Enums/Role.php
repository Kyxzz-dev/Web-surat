<?php

namespace App\Enums;

enum Role
{
    case SUPER_ADMIN;
    case ADMIN;
    case STAFF;

    public function status(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'super-admin',
            self::ADMIN => 'admin',
            self::STAFF => 'staff',
        };
    }
}