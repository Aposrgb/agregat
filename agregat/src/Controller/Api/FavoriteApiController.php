<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Helper\Exception\ApiException;
use App\Helper\Filter\ProductsFilter;
use App\Repository\ProductsRepository;
use App\Service\FavoriteService;
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
 * @OA\Tag(name="Favorite")
 *
 * @OA\Response(
 *     response="401",
 *     description="Unauthorized",
 *     @OA\JsonContent(ref="#/components/schemas/ApiException")
 * )
 */
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/favorite')]
class FavoriteApiController extends AbstractController
{
    /**
     * Добавление продукта в избранное
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data",type="object",
     *              ref=@Model(type="App\Entity\Products", groups={"get_products"})
     *          )
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
    #[Route('/{productId}', name: 'add_favorite_product', methods: ["PUT"])]
    public function addFavorite(
        string|int             $productId,
        ValidatorService       $validatorService,
        ProductsService        $productsService,
        FavoriteService        $favoriteService,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        $validatorService->validateMaxRangeInteger($productId);
        $product = $productsService->getProductById($productId);
        /** @var User $user */
        $user = $this->getUser();
        if ($favoriteService->getFavoriteProductIfExist($user, $product)) {
            throw new ApiException(message: 'Товар уже был добавлен ранее');
        }
        $user->addFavoritesProduct($product);
        $entityManager->flush();
        return $this->json(
            data: ['data' => $product],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_products']]
        );
    }

    /**
     * Удаление продукта из избранного
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
     */
    #[Route('/{productId}', name: 'delete_favorite', methods: ['DELETE'])]
    public function deleteFavorite(
        int|string             $productId,
        ValidatorService       $validatorService,
        ProductsService        $productsService,
        EntityManagerInterface $entityManager,
        FavoriteService        $favoriteService,
    ): JsonResponse
    {
        $validatorService->validateMaxRangeInteger($productId);
        $product = $productsService->getProductById($productId);
        /** @var User $user */
        $user = $this->getUser();
        $favoriteService->getFavoriteProductIfExist($user, $product);
        $user->removeFavoritesProduct($product);
        $entityManager->flush();
        return $this->json(
            data: [], status: Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Получение избранного
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
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data",type="object",
     *              ref=@Model(type="App\Entity\Products", groups={"get_products"})
     *          ),
     *          @OA\Property(property="count", type="integer"),
     *          @OA\Property(property="pageCount", type="integer"),
     *          @OA\Property(property="currentPage", type="integer")
     *     )
     * )
     *
     */
    #[Route('', name: 'get_favorite', methods: ['GEt'])]
    public function getFavorites(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorService    $validatorService,
        ProductsRepository  $productsRepository,
    ): JsonResponse
    {
        /** @var ProductsFilter $productsFilter */
        $productsFilter = $serializer->deserialize(
            json_encode($request->query->all()), ProductsFilter::class, 'json'
        );
        $validatorService->validate($productsFilter, ['filter']);
        /** @var User $user */
        $user = $this->getUser();
        $ids = array_map(function ($item) {
            return $item->getId();
        }, $user->getFavoritesProducts()->toArray());

        if (empty($ids)) {
            $paginator = [];
            $count = 0;
        } else {
            $paginator = $productsRepository->getProductWithIds($productsFilter, $ids);
            $count = $paginator->count();
            $paginator = $paginator->getQuery()->getResult();
        }

        return $this->json(
            data: [
                "data" => $paginator,
                "count" => $count,
                "pageCount" => ceil($count / $productsFilter->getPagination()->getLimit()),
                "currentPage" => (int)$productsFilter->getPagination()->getPage()
            ],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_products']]

        );
    }
}