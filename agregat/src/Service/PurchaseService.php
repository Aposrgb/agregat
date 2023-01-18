<?php

namespace App\Service;

use App\Entity\Products;
use App\Entity\Purchase;
use App\Entity\User;
use App\Helper\DTO\PurchaseDTO;
use App\Helper\EnumStatus\DeliveryStatus;
use App\Helper\EnumStatus\PurchaseStatus;
use App\Helper\EnumType\PurchaseAddressType;
use App\Helper\Exception\ApiException;
use App\Repository\PurchaseRepository;

class PurchaseService
{
    public function __construct(
        protected PurchaseRepository $purchaseRepository
    )
    {
    }

    public function createPurchase(Products $products, PurchaseDTO $purchaseDTO, User $user): Purchase
    {
        if (!$user->getAddress()) {
            throw new ApiException(message: 'У пользователя нет адреса для доставки');
        }
        $purchase = (new Purchase())
            ->setStatus(PurchaseStatus::PURCHASED->value)
            ->setDeliveryStatus(DeliveryStatus::IN_PROCESS->value)
            ->setOwner($user)
            ->setPrice($purchaseDTO->getCount() * $products->getPrice())
            ->setCount($purchaseDTO->getCount())
            ->setProduct($products)
            ->setPhone($purchaseDTO->getPhone())
            ->setName($purchaseDTO->getName())
            ->setSurname($purchaseDTO->getSurname())
            ->setDeliveryService(PurchaseAddressType::POST_RUSSIA->value)
            ->setDeliveryAddress($purchaseDTO->getAddress());

        $this->purchaseRepository->save($purchase, true);
        return $purchase;
    }
}