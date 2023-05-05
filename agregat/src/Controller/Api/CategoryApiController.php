<?php

namespace App\Controller\Api;

use App\Helper\Filter\CategoriesFilter;
use App\Repository\CategoriesRepository;
use App\Repository\SubCategoriesRepository;
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
 * @OA\Tag(name="Category")
 */
#[Route('/category')]
class CategoryApiController extends AbstractController
{
    /**
     * Получение категорий
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
     *                  ref=@Model(type="App\Entity\Categories", groups={"get_categories"})
     *              )
     *          ),
     *          @OA\Property(property="count", type="integer"),
     *          @OA\Property(property="pageCount", type="integer"),
     *          @OA\Property(property="currentPage", type="integer")
     *     )
     * )
     *
     */
    #[Route('', name: 'get_category', methods: ['GET'])]
    public function getCategory(
        Request              $request,
        SerializerInterface  $serializer,
        ValidatorService     $validatorService,
        CategoriesRepository $categoriesRepository,
    ): JsonResponse
    {
        $query = $request->query->all();
        /** @var CategoriesFilter $categoriesFilter */
        $categoriesFilter = $serializer->deserialize(
            json_encode($query), CategoriesFilter::class, 'json'
        );
        $validatorService->validate($categoriesFilter, ['filter']);
        $paginator = $categoriesRepository->getCategoriesByFilter($categoriesFilter);
        $count = $paginator->count();

        return $this->json(
            data: [
                "data" => $paginator->getQuery()->getResult(),
                "count" => $count,
                "pageCount" => ceil($count / $categoriesFilter->getPagination()->getLimit()),
                "currentPage" => (int)$categoriesFilter->getPagination()->getPage()
            ],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_categories']]

        );
    }

    /**
     * Получение подкатегорий
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
     *                  ref=@Model(type="App\Entity\SubCategories", groups={"get_filter"})
     *              )
     *          ),
     *          @OA\Property(property="count", type="integer"),
     *          @OA\Property(property="pageCount", type="integer"),
     *          @OA\Property(property="currentPage", type="integer")
     *     )
     * )
     *
     */
    #[Route('/sub', name: 'get_sub_category', methods: ['GET'])]
    public function getSubCategory(
        Request                 $request,
        SerializerInterface     $serializer,
        ValidatorService        $validatorService,
        SubCategoriesRepository $subCategoriesRepository,
    ): JsonResponse
    {
        $query = $request->query->all();
        /** @var CategoriesFilter $categoriesFilter */
        $categoriesFilter = $serializer->deserialize(
            json_encode($query), CategoriesFilter::class, 'json'
        );
        $validatorService->validate($categoriesFilter, ['filter']);
        $paginator = $subCategoriesRepository->getCategoriesByFilter($categoriesFilter);
        $count = $paginator->count();

        return $this->json(
            data: [
                "data" => $paginator->getQuery()->getResult(),
                "count" => $count,
                "pageCount" => ceil($count / $categoriesFilter->getPagination()->getLimit()),
                "currentPage" => (int)$categoriesFilter->getPagination()->getPage()
            ],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_filter']]

        );
    }
}