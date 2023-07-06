<?php

namespace App\Helper\DTO\Response;

use App\Entity\Basket;

class BasketResponse
{
    public ?int $id;
    public ?int $count;
    public ?UserResponse $owner;
    public ?ProductResponse $product;

    public function __construct(Basket $basket, ?ProductResponse $productResponse)
    {
        $this->id = $basket->getId();
        $this->count = $basket->getCount();
        $this->owner = $basket->getOwner() ? new UserResponse($basket->getOwner()) : null;
        $this->product = $productResponse;
    }

}