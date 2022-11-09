<?php

namespace App\Helper\EnumStatus;

enum FeedbackStatus: int
{
    case ACTIVE = 1;
    case IN_PROGRESS = 2;
    case CONFIRMED = 3;

    public static function getStatuses(): array
    {
        return [
            'Активный' => self::ACTIVE->value,
            'В прогрессе' => self::IN_PROGRESS->value,
            'Выполнен' => self::CONFIRMED->value,
        ];
    }
}