<?php

namespace App\Helper\EnumType;

enum SettingsType: int
{
    case SENDER_NAME = 1;

    public function getName(): string
    {
        return match ($this){
            self::SENDER_NAME => 'Почта для рассылки'
        };
    }
}