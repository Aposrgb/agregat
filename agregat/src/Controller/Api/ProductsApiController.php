<?php

namespace App\Controller\Api;

use App\Helper\Exception\ApiException;
use App\Helper\Filter\ProductsFilter;
use App\Repository\ProductsRepository;
use App\Service\ProductsService;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="Products")
 */
#[Route('/products')]
class ProductsApiController extends AbstractController
{
    /**
     * Получение продуктов
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
     *     response="400",
     *     description="Not valid data",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="array",
     *              @OA\Items(
     *                  ref=@Model(type="App\Entity\Products", groups={"get_products"})
     *              )
     *          ),
     *          @OA\Property(property="count", type="integer"),
     *          @OA\Property(property="pageCount", type="integer"),
     *          @OA\Property(property="currentPage", type="integer")
     *     )
     * )
     *
     */
    #[Route('', name: 'get_products', methods: ['GET'])]
    public function getProducts(
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
        $paginator = $productsRepository->getProductsByFilter($productsFilter);
        $count = $paginator->count();

        return $this->json(
            data: [
                "data" => $paginator->getQuery()->getResult(),
                "count" => $count,
                "pageCount" => ceil($count / $productsFilter->getPagination()->getLimit()),
                "currentPage" => (int)$productsFilter->getPagination()->getPage()
            ],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_products']]

        );
    }

    /**
     * Загрузка фото для продукта
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data",type="object",
     *              @OA\Property(property="link", type="string")
     *          )
     *     )
     * )
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="image", type="file", description="Изображение jpg, png"),
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
     *     description="Failed to delete image",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     */
    #[Route('/{productId}/img', name: 'upload_img', methods: ["POST"])]
    public function uploadImg(
        string|int             $productId,
        Request                $request,
        ValidatorService       $validatorService,
        EntityManagerInterface $entityManager,
        ProductsService        $productsService,
    ): JsonResponse
    {
        $validatorService->validateMaxRangeInteger($productId);
        $product = $productsService->getProductById($productId);
        $fileImage = $request->files->get('image');
        if ($fileImage) {
            $validatorService->validateImagesExtension(
                [$fileImage], ProductsService::AVAILABLE_IMAGE_EXTENSIONS
            );
            $product = $productsService->uploadImg($product, $fileImage);
        } else {
            throw new ApiException(message: 'Нет изображения');
        }
        $entityManager->flush();
        return $this->json(
            data: ['data' => [
                'path' => $product->getImg()
            ]]
        );
    }
}