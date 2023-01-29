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
        $productIds = array_map(function (ProductDTO $productDTO) {
            return $productDTO->getProductId();
        }, $purchaseDTO->getProducts());
        $products = $this->productsRepository->findBy([
            'id' => $productIds
        ]);
        if (count($productIds) != count($products)) {
            throw new ApiException(message: 'Не найден продукт', status: Response::HTTP_NOT_FOUND);
        }
        $purchases = [];
        /** @var ProductDTO $productDTO */
        foreach ($purchaseDTO->getProducts() as $productDTO) {
            foreach ($products as $product) {
                if ($product->getId() == $productDTO->getProductId()) {
                    $purchase = (new Purchase())
                        ->setStatus(PurchaseStatus::PURCHASED->value)
                        ->setDeliveryStatus(DeliveryStatus::IN_PROCESS->value)
                        ->setOwner($user)
                        ->setPrice($productDTO->getCount() * $product->getPrice())
                        ->setCount($productDTO->getCount())
                        ->setProduct($product)
                        ->setPhone($purchaseDTO->getPhone())
                        ->setName($purchaseDTO->getName())
                        ->setSurname($purchaseDTO->getSurname())
                        ->setDeliveryService($purchaseDTO->getDeliveryService())
                        ->setDeliveryAddress($purchaseDTO->getAddress());

                    $this->purchaseRepository->save($purchase);
                    $purchases[] = $purchase;
                }
            }
        }
        $this->basketRepository->deleteBasketByUserProducts($user->getId(), $productIds);
        $this->entityManager->flush();
        $this->mailerService->sendMailTemplate('mail/mailer.html.twig', 'Поступил Заказ на АгрегатЕКБ', context: [
            'purchases' => $purchases,
            'user' => $user
        ]);
    }
}