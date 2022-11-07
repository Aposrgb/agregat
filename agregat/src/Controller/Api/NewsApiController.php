<?php

namespace App\Controller\Api;

use App\Entity\News;
use App\Helper\Filter\NewsFilter;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ValidatorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="News")
 */
#[Route('/news')]
class NewsApiController extends AbstractController
{

    /**
     * Получение новостей
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
     *     name="filter[year]",
     *     description="2022 - по умолчанию",
     *     @OA\Schema(type="integer", default="2022")
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
     *                  ref=@Model(type="App\Entity\News", groups={"get_news"})
     *              )
     *          ),
     *          @OA\Property(property="count", type="integer"),
     *          @OA\Property(property="pageCount", type="integer"),
     *          @OA\Property(property="currentPage", type="integer")
     *     )
     * )
     *
     */
    #[Route('', name: 'get_news', methods: ['GET'])]
    public function getNews(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorService    $validatorService,
        NewsRepository      $newsRepository
    ): JsonResponse
    {
        $query = $request->query->all();
        $newsFilter = $serializer->deserialize(
            json_encode(
                array_merge($query, $query['filter'] ?? [])
            ),
            NewsFilter::class, 'json');
        $validatorService->validate($newsFilter, ['filter']);
        $paginator = $newsRepository->getNewsByFilter($newsFilter);
        $count = $paginator->count();

        return $this->json(
            data: [
                "data" => $paginator->getQuery()->getResult(),
                "count" => $count,
                "pageCount" => ceil($count / $newsFilter->getPagination()->getLimit()),
                "currentPage" => (int)$newsFilter->getPagination()->getPage()
            ],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_news']]
        );
    }

    /**
     * Получение новости по id
     *
     * @OA\Response(
     *     response="404",
     *     description="Not found",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *             ref=@Model(type="App\Entity\News", groups={"get_news"})
     *          )
     *     )
     * )
     *
     */
    #[Route('/{news<\d+>}', name: 'get_news_by_id', methods: ['GET'])]
    public function getNewsById(
        News $news
    ): JsonResponse
    {
        return $this->json(
            data: [
                "data" => $news,
            ],
            status: Response::HTTP_OK,
            context: ['groups' => ['get_news']]
        );
    }
}