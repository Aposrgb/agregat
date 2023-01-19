<?php

namespace App\Helper\EnumType;

enum PurchaseAddressType: int
{
    case POINT_ISSUE = 1;
    case POST_RUSSIA = 2;
    case PICKUP = 3;
    case DELIVERY = 4;

    public static function getTypes(): array
    {
        return [
            self::POINT_ISSUE->value,
            self::POST_RUSSIA->value,
            self::PICKUP->value,
            self::DELIVERY->value,
        ];
    }

    public function getTypeName(): ?string
    {
        return match ($this){
            self::POINT_ISSUE => "Пункт выдачи",
            self::POST_RUSSIA => "Почта России",
            self::PICKUP => "Самовывоз",
            self::DELIVERY => "Доставка",
        };
    }
}