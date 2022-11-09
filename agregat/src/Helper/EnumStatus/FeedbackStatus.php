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
            '��������' => self::ACTIVE->value,
            '� ���������' => self::IN_PROGRESS->value,
            '��������' => self::CONFIRMED->value,
        ];
    }
}