<?php

namespace App\Service;

use App\Entity\Products;
use App\Entity\Purchase;
use App\Entity\User;
use App\Helper\DTO\ProductDTO;
use App\Helper\DTO\PurchaseDTO;
use App\Helper\EnumStatus\DeliveryStatus;
use App\Helper\EnumStatus\PurchaseStatus;
use App\Helper\Exception\ApiException;
use App\Repository\BasketRepository;
use App\Repository\ProductsRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PurchaseService
{
    public function __construct(
        protected PurchaseRepository     $purchaseRepository,
        protected ProductsRepository     $productsRepository,
        protected EntityManagerInterface $entityManager,
        protected BasketRepository       $basketRepository,
        protected MailerService          $mailerService,
    )
    {
    }

    public function createPurchase(PurchaseDTO $purchaseDTO, User $user): void
    {
        $baskets = $this->basketRepository->findBy([
            'owner' => $user
        ]);
        $purchases = [];
        $productIds = [];
        /** @var ProductDTO $productDTO */
        foreach ($baskets as $basket) {
            $purchase = (new Purchase())
                ->setStatus(PurchaseStatus::PURCHASED->value)
                ->setDeliveryStatus(DeliveryStatus::IN_PROCESS->value)
                ->setOwner($user)
                ->setPrice($basket->getCount() * $basket->getProduct()->getPrice())
                ->setCount($basket->getCount())
                ->setProduct($basket->getProduct())
                ->setPhone($purchaseDTO->getPhone())
                ->setName($purchaseDTO->getName())
                ->setSurname($purchaseDTO->getSurname())
                ->setDeliveryService($purchaseDTO->getDeliveryService())
                ->setDeliveryAddress($purchaseDTO->getAddress());
            $productIds[]  = $basket->getProduct()->getId();
            $this->purchaseRepository->save($purchase);
            $purchases[] = $purchase;
        }
        $this->basketRepository->deleteBasketByUserProducts($user->getId(), $productIds);
        $this->entityManager->flush();
        $this->mailerService->sendMailTemplate('mail/mailer.html.twig', 'Поступил Заказ на АгрегатЕКБ', context: [
            'purchases' => $purchases,
            'user' => $user
        ]);
    }
}