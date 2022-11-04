<?php

namespace App\Controller\Api;

use App\Entity\Basket;
use App\Helper\DTO\BasketDTO;
use App\Helper\Exception\ApiException;
use App\Repository\BasketRepository;
use App\Service\BasketService;
use App\Service\ProductsService;
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
 * @OA\Tag(name="Basket")
 *
 * @OA\Response(
 *     response="401",
 *     description="Unauthorized",
 *     @OA\JsonContent(ref="#/components/schemas/ApiException")
 * )
 */
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/basket')]
class BasketApiController extends AbstractController
{
    /**
     * Добавление продукта в корзину
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data",type="object",
     *              ref=@Model(type="App\Entity\Basket", groups={"get_basket"})
     *          )
     *     )
     * )
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         ref=@Model(type="App\Helper\DTO\BasketDTO", groups={"add_basket"})
     *     )
     * )
     *
     * @OA\Parameter(
     *     in="path",
     *     description="id - Продукта",
     *     name="productId",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Data not valid",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     */
    #[Route('/{productId}', name: 'add_basket_product', methods: ["PUT"])]
    public function addBasket(
        string|int          $productId,
        Request             $request,
        ValidatorService    $validatorService,
        ProductsService     $productsService,
        SerializerInterface $serializer,
        BasketRepository    $basketRepository,
        BasketService       $basketService,
    ): JsonResponse
    {
        $validatorService->validateMaxRangeInteger($productId);
        $product = $productsService->getProductById($productId);
        if ($basketService->getBasketByUserAndProduct($this->getUser(), $product)) {
            throw new ApiException(message: 'Товар уже был добавлен ранее');
        }
        $basketDTO = $serializer->deserialize($request->getContent(), BasketDTO::class, 'json');
        $validatorService->validate($basketDTO, ['add_basket']);
        $basket = (new Basket())
            ->setOwner($this->getUser())
            ->setCount($basketDTO->getCount())
            ->setProduct($product);
        $basketRepository->save($basket, true);
        return $this->json(
            data: ['data' => $basket],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_basket']]
        );
    }

    /**
     * Удаление продукта из корзины
     *
     * @OA\Response(
     *     response="204",
     *     description="Success"
     * )
     *
     * @OA\Parameter(
     *     in="path",
     *     description="id - Продукта",
     *     name="productId",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     *
     */
    #[Route('/{productId}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteBasket(
        int|string       $productId,
        ValidatorService $validatorService,
        ProductsService  $productsService,
        BasketService    $basketService,
        BasketRepository $basketRepository
    ): JsonResponse
    {
        $validatorService->validateMaxRangeInteger($productId);
        $product = $productsService->getProductById($productId);
        $basket = $basketService->getBasketByUserAndProduct($this->getUser(), $product);
        if (!$basket) {
            throw new ApiException(message: 'Товара нет в корзине');
        }
        $basketRepository->remove($basket, true);
        return $this->json(
            data: [], status: Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Получение корзины
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data",type="object",
     *              ref=@Model(type="App\Entity\Basket", groups={"get_baskets"})
     *          )
     *     )
     * )
     *
     */
    #[Route('', name: 'get_basket', methods: ['GEt'])]
    public function getBaskets(): JsonResponse
    {
        return $this->json(
            data: ['data' => $this->getUser()->getBaskets()],
            context: ['groups' => ['get_baskets']]
        );
    }

    /**
     * Изменение кол-ва продуктов в корзине
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data",type="object",
     *              ref=@Model(type="App\Entity\Basket", groups={"get_baskets"})
     *          )
     *     )
     * )
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         ref=@Model(type="App\Helper\DTO\BasketDTO", groups={"edit_count"})
     *     )
     * )
     *
     * @OA\Parameter(
     *     in="path",
     *     description="id - Продукта",
     *     name="productId",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Data not valid",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     */
    #[Route('/{productId}', name: 'edit_count', methods: ['PATCH'])]
    public function editCount(
        string|int             $productId,
        Request                $request,
        ValidatorService       $validatorService,
        ProductsService        $productsService,
        SerializerInterface    $serializer,
        EntityManagerInterface $entityManager,
        BasketService          $basketService
    ): JsonResponse
    {
        $validatorService->validateMaxRangeInteger($productId);
        $product = $productsService->getProductById($productId);
        /** @var BasketDTO $basketDTO */
        $basketDTO = $serializer->deserialize($request->getContent(), BasketDTO::class, 'json');
        $validatorService->validate($basketDTO, ['edit_count']);
        $basket = $basketService->getBasketByUserAndProduct($this->getUser(), $product);
        $basket->setCount($basketDTO->getCount());
        $entityManager->flush();
        return $this->json(
            data: ['data' => $basket],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_basket']]
        );
    }
}