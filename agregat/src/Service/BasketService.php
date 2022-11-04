<?php

namespace App\Service;

use App\Entity\Basket;
use App\Entity\Products;
use App\Entity\User;

class BasketService
{
    public function getBasketByUserAndProduct(User $user, Products $products): ?Basket
    {
        $baskets = $user->getBaskets();
        foreach ($baskets as $basket) {
            if ($basket->getProduct() === $products) {
                return $basket;
            }
        }
        return null;
    }
}