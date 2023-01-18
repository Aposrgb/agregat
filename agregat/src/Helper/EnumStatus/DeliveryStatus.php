<?php

namespace App\Helper\EnumStatus;

enum DeliveryStatus: int
{
    case WAIT_DELIVERY = 1;
    case IN_PROCESS = 2;
    case DELIVERED = 3;

    public function getTypeName(): string
    {
        return match($this){
            self::WAIT_DELIVERY => "Ожидание доставки",
            self::IN_PROCESS => "Передаем в доставку",
            self::DELIVERED => "Доставлено"
        };
    }
}