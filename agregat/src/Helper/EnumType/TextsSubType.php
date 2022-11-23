<?php

namespace App\Helper\EnumType;

enum TextsSubType: int
{
    case ADDRESS = 1;
    case EMAIL = 2;
    case PHONE = 3;
    case WORK_TIME  = 4;
    case ABOUT_COMPANY = 5;
    case TERM_DELIVERY = 6;
    case TERM_PAYMENTS = 7;
    case PRODUCT_GUARANTEE = 8;

    public function getTypeName(): string
    {
        return match($this){
            self::ADDRESS => 'Адрес',
            self::EMAIL => 'E-mail',
            self::PHONE => 'Телефон',
            self::WORK_TIME => 'Время работы',
            self::ABOUT_COMPANY => 'О Компании',
            self::TERM_DELIVERY => 'Условия доставки',
            self::TERM_PAYMENTS => 'Условия оплаты',
            self::PRODUCT_GUARANTEE => 'Гарантия на товар'
        };
    }
}