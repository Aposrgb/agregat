<?php

namespace App\Controller\Api;

use App\Entity\Products;
use App\Helper\DTO\PurchaseDTO;
use App\Helper\Filter\PurchaseFilter;
use App\Repository\PurchaseRepository;
use App\Service\PurchaseService;
use App\Service\ValidatorService;
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
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="data", type="object",
     *             ref=@Model(type="App\Entity\Purchase", groups={"get_purchase"})
     *         )
     *     )
     * )
     *
     * @OA\Parameter(
     *     name="product",
     *     in="path",
     *     description="id - product",
     *     @OA\Schema(type="integer")
     * )
     */
    #[Route('/{product<\d+>}', name: 'create_purchase', methods: ['POST'])]
    public function addPurchase(
        Products            $product,
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

        return $this->json(
            data: ['data' => $purchaseService->createPurchase($product, $purchaseDTO, $this->getUser())],
            status: Response::HTTP_CREATED,
            context: ['groups' => ['get_purchase']]
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
     *         @OA\Property(property="data",type="object",
     *              ref=@Model(type="App\Entity\Purchase", groups={"get_purchase_user"})
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
}