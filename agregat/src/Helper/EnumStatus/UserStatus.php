<?php

namespace App\Helper\EnumStatus;

enum UserStatus: int
{
    case CONFIRMED = 1;
    case BLOCKED = 11;

    public function getStatus(): string
    {
        return match($this){
            self::CONFIRMED => 'Подтвержден',
            self::BLOCKED => 'Заблокирован'
        };
    }

    public static function getStatuses(): array
    {
        return [
            UserStatus::CONFIRMED->value,
            UserStatus::BLOCKED->value
        ];
    }
}
