<?php

namespace App\Helper\EnumStatus;

enum PurchaseStatus: int
{
    case WAIT_PAYMENT = 0;
    case PURCHASED = 1;
    case CANCELLED = 11;

    public function getTypeName(): string
    {
        return match ($this) {
            self::WAIT_PAYMENT => "Ожидание доставки",
            self::PURCHASED => "Оплачено",
            self::CANCELLED => "Отменено"
        };
    }

    public static function getTypesName(): array
    {
        return [
            "Ожидание доставки" => self::WAIT_PAYMENT->value,
            "Оплачено" => self::PURCHASED->value,
            "Отменено" => self::CANCELLED->value,
        ];
    }
}