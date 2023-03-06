<?php

namespace App\Controller\Api;

use App\Entity\Products;
use App\Helper\Exception\ApiException;
use App\Helper\Filter\ProductsFilter;
use App\Repository\ProductsRepository;
use App\Service\MailerService;
use App\Service\ProductsService;
use App\Service\ValidatorService;
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
     * @OA\Parameter(
     *     in="query",
     *     name="filter[minPrice]",
     *     description="Мин. цена",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="filter[maxPrice]",
     *     description="Макс. цена",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="filter[minRating]",
     *     description="Мин. рейтинг",
     *     @OA\Schema(type="float")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="filter[maxRating]",
     *     description="Макс. рейтинг",
     *     @OA\Schema(type="float")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="filter[isActual]",
     *     description="Актуальность",
     *     @OA\Schema(type="boolean")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="search[categoryId]",
     *     description="id - категории",
     *     example="1,2,3,4",
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="search[subCategoryId]",
     *     description="id - категории",
     *     example="1,2,3,4",
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="search[name]",
     *     description="поиск по имени",
     *     @OA\Schema(type="string")
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
        $query = $request->query->all();
        /** @var ProductsFilter $productsFilter */
        $productsFilter = $serializer->deserialize(
            json_encode(
                array_merge($query, $query['filter'] ?? [], $query['search'] ?? [])
            ), ProductsFilter::class, 'json'
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
     * Детали продукта
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\Products", groups={"get_product_detail"})
     *          )
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
    #[Route('/{product<\d+>}', name: 'get_product', methods: ['GET'])]
    public function getProduct(
        Products $product,
    ): JsonResponse
    {
        return $this->json(data: ['data' => $product], context: ['groups' => ['get_product_detail']]);
    }

    /**
     * Получение фильтров
     *
     * @OA\Response(
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="data", type="object",
     *             ref=@Model(type="App\Helper\Mapped\ProductFilter", groups={"get_filter"})
     *         )
     *     )
     * )
     *
     *
     * @OA\Parameter(
     *     in="query",
     *     name="filter[isActual]",
     *     description="Актуальность",
     *     @OA\Schema(type="boolean")
     * )
     *
     * @OA\Parameter(
     *     in="query",
     *     name="filter[category]",
     *     description="id - категории",
     *     @OA\Schema(type="integer")
     * )
     *
     */
    #[Route('/filter', name: 'get_filter', methods: ['GET'])]
    public function getFilters(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorService    $validatorService,
        ProductsRepository  $productsRepository,
        ProductsService     $productsService): JsonResponse
    {
        $query = $request->query->all();
        /** @var ProductsFilter $productsFilter */
        $productsFilter = $serializer->deserialize(
            json_encode(
                array_merge($query['filter'] ?? [])
            ), ProductsFilter::class, 'json'
        );
        $validatorService->validate($productsFilter, ['filter']);
        $products = $productsRepository->findAll();
        $filter = $productsService->getFilterByProducts($products, $productsFilter);

        return $this->json(['data' => $filter], context: ['groups' => ['get_filter']]);
    }

    /**
     * Заказать звонок
     *
     * @OA\Response(
     *     response="200",
     *     description="success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="ok")
     *     )
     * )
     *
     * @OA\Parameter(
     *     in="path",
     *     required=true,
     *     name="phone",
     *     @OA\Schema(type="string")
     * )
     *
     */
    #[Route('/call/{phone}', name: 'send_order', methods: ['GET'])]
    public function sendOrder(
        string        $phone,
        MailerService $mailerService,
    ): JsonResponse
    {
        if (!$phone or strlen($phone) < 5) {
            throw new ApiException(message: 'Нет телефона или длина телефона слишком коротка');
        }
        if (str_contains($phone, '+')) {
            if (!is_numeric(substr($phone, 1))) {
                throw new ApiException(message: 'Неверный телефон');
            }
        } else {
            if (!is_numeric($phone)) {
                throw new ApiException(message: 'Неверный телефон');
            }
        }
        $mailerService->sendMailTemplate('mail/mailer.html.twig', 'Заказали звонок АгрегатЕКБ', context: [
            'phone' => $phone
        ]);
        return $this->json(data: ['message' => 'ok'], status: Response::HTTP_OK);
    }

    /**
     * Загрузка xml из 1С
     *
     * @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(property="xmlFile", type="file", description="Xml - файл"),
     *          )
     *     )
     * )
     */
//    #[Route('/import/xml', name: 'import_products_xml', methods: ["POST"])]
    public function importXML(
        Request $request,

    ): JsonResponse
    {
        $xmlFile = $request->files->get('xmlFile');
        return $this->json(
            data: ['data' => []]
        );
    }
}