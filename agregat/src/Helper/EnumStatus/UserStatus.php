<?php

namespace App\Helper\EnumStatus;

enum UserStatus: int
{
    case CONFIRMED = 1;
    case ON_MODERATION = 2;
    case NO_REGISTERED = 3;
    case BLOCKED = 11;

    public function getStatus(): string
    {
        return match($this){
            self::CONFIRMED => 'Подтвержден',
            self::ON_MODERATION => 'На модерации',
            self::NO_REGISTERED => 'Анкета не заполнена',
            self::BLOCKED => 'Заблокирован'
        };
    }

    public static function getStatuses(): array
    {
        return [
            UserStatus::ON_MODERATION->value,
            UserStatus::CONFIRMED->value,
            UserStatus::BLOCKED->value,
            UserStatus::NO_REGISTERED->value
        ];
    }
}
