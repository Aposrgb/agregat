<?php

namespace App\Helper\EnumStatus;

enum DeviceStatus: int
{
    case ACTIVE = 1;
    case DEBOUNCE = 2;
    case NOT_FULL_REGISTRATION = 3;
    case RESET_PASSWORD = 9;
    case EXPIRED = 21;
    case INACTIVE = 10;

    public function getStatus()
    {
        return match ($this) {
            self::ACTIVE => 'Активный',
            self::NOT_FULL_REGISTRATION => 'Не полная регистрация',
            self::RESET_PASSWORD => 'Сброс пароля',
            self::INACTIVE => 'Не активный',
            self::EXPIRED => 'Истек',
            self::DEBOUNCE => 'Дебаунс',
        };
    }
}
