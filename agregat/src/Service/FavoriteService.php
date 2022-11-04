<?php

namespace App\Service;

use App\Entity\Products;
use App\Entity\User;

class FavoriteService
{
    public function getFavoriteProductIfExist(User $user, Products $products): ?Products
    {
        $favoriteProducts = $user->getFavoritesProducts();
        foreach ($favoriteProducts as $favoriteProduct) {
            if ($favoriteProduct === $products) {
                return $products;
            }
        }
        return null;
    }
}