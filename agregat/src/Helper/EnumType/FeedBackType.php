<?php

namespace App\Helper\EnumType;

enum FeedBackType: int
{
    case CALL = 2;
    case FEEDBACK = 1;

    public static function getType($type): ?FeedBackType
    {
        return match ($type){
            self::CALL->value, self::CALL->value . '' => self::CALL,
            self::FEEDBACK->value, self::FEEDBACK->value . '' => self::FEEDBACK,
            default => null
        };
    }
}