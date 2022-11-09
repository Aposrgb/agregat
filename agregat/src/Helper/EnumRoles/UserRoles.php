<?php

namespace App\Helper\EnumRoles;

enum UserRoles: string
{
    case ROLE_ADMIN = "ROLE_ADMIN";
    case ROLE_USER = "ROLE_USER";

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN->value,
            self::ROLE_USER->value,
        ];
    }
}