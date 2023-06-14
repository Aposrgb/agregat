<?php

namespace App\Controller\Api;

use App\Entity\Products;
use App\Entity\Purchase;
use App\Helper\DTO\ProductDTO;
use App\Helper\DTO\PurchaseDTO;
use App\Helper\EnumStatus\DeliveryStatus;
use App\Helper\EnumStatus\PurchaseStatus;
use App\Helper\Exception\ApiException;
use App\Helper\Filter\PurchaseFilter;
use App\Repository\PurchaseRepository;
use App\Service\PurchaseService;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="Purchase")
 */
#[Route('/purchase')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class PurchaseApiController extends AbstractController
{
    /**
     * Добавить в заказ продукт
     *
     * @OA\RequestBody(
     *     description="Покупка продукта",
     *     @OA\JsonContent(
     *         ref=@Model(type="App\Helper\DTO\PurchaseDTO", groups={"create_purchase"})
     *     )
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="204",
     *     description="Success"
     * )
     *
     */
    #[Route('', name: 'create_purchase', methods: ['POST'])]
    public function addPurchase(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorService    $validatorService,
        PurchaseService     $purchaseService,
    ): JsonResponse
    {
        /** @var PurchaseDTO $purchaseDTO */
        $purchaseDTO = $serializer->deserialize(
            $request->getContent(),
            PurchaseDTO::class,
            'json'
        );
        $validatorService->validate($purchaseDTO, ['create_purchase']);
        $purchaseService->createPurchase($purchaseDTO, $this->getUser());
        return $this->json(
            data: [],
            status: Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Получение заказов пользователя
     *
     * @OA\Parameter(
     *     in="query",
     *     name="pagination[page]",
     *     description="1 страница по умолчанию",
     *     @OA\Schema(type="integer", default="1")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="pagination[limit]",
     *     description="10 элементов по умолчанию",
     *     @OA\Schema(type="integer", default="10")
     * )
     *
     * @OA\Response(
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="data",type="array",
     *              @OA\Items(ref=@Model(type="App\Entity\Purchase", groups={"get_purchase_user"}))
     *          ),
     *          @OA\Property(property="count", type="integer"),
     *          @OA\Property(property="pageCount", type="integer"),
     *          @OA\Property(property="currentPage", type="integer")
     *     )
     * )
     *
     */
    #[Route('', name: 'purchase_user', methods: ['GET'])]
    public function getPurchasesUser(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorService    $validatorService,
        PurchaseRepository  $purchaseRepository,
    ): JsonResponse
    {
        /** @var PurchaseFilter $purchaseFilter */
        $purchaseFilter = $serializer->deserialize(
            json_encode($request->query->all()), PurchaseFilter::class, 'json'
        );
        $validatorService->validate($purchaseFilter, ['filter']);
        $paginator = $purchaseRepository->findPurchaseByUser($purchaseFilter, $this->getUser()->getId());
        $count = $paginator->count();

        return $this->json(
            data: [
                "data" => $paginator->getQuery()->getResult(),
                "count" => $count,
                "pageCount" => ceil($count / $purchaseFilter->getPagination()->getLimit()),
                "currentPage" => (int)$purchaseFilter->getPagination()->getPage()
            ],
            context: ['groups' => ['get_purchase_user']]
        );
    }

    /**
     * Отмена покупки
     *
     * @OA\Response(
     *     response="204",
     *     description="Success"
     * )
     *
     * @OA\Parameter(
     *     in="path",
     *     required=true,
     *     name="purchase",
     *     @OA\Schema(type="integer")
     * )
     *
     */
    #[Route('/{purchase<\d+>}', name: 'cancel_purchase', methods: ['PATCH'])]
    public function cancelPurchase(
        Purchase               $purchase,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        if ($purchase->getOwner() !== $this->getUser()) {
            throw new ApiException(message: 'Покупка не найдена', status: Response::HTTP_NOT_FOUND);
        }
        if ($purchase->getStatus() == PurchaseStatus::CANCELLED->value) {
            throw new ApiException(message: 'Покупка была отменена ранее');
        }
        $purchase
            ->setStatus(PurchaseStatus::CANCELLED->value)
            ->setDeliveryStatus(DeliveryStatus::CLOSED->value);
        $entityManager->flush();
        return $this->json(
            data: [],
            status: Response::HTTP_NO_CONTENT
        );
    }
}
