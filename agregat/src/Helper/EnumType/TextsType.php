<?php

namespace App\Helper\EnumType;

enum TextsType: int
{
    case CONTACTS = 1;
    case HOW_TO_BUY = 2;

    public function getTypeName(): string
    {
        return match ($this){
            self::CONTACTS => 'Контакты',
            self::HOW_TO_BUY => 'Как купить'
        };
    }

    public function getSubTypes(): array
    {
        return match ($this) {
            self::CONTACTS => [
                TextsSubType::ADDRESS->value,
                TextsSubType::EMAIL->value,
                TextsSubType::PHONE->value,
                TextsSubType::WORK_TIME->value,
                TextsSubType::ABOUT_COMPANY->value,
            ],
            self::HOW_TO_BUY => [
                'Условия покупки' => TextsSubType::TERM_PAYMENTS->value,
                'Гарантии на товар' => TextsSubType::PRODUCT_GUARANTEE->value,
                'Условия доставки' => TextsSubType::TERM_DELIVERY->value
            ]
        };
    }
}