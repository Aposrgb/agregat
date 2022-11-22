<?php

namespace App\Controller\Api;

use App\Entity\Comments;
use App\Entity\Products;
use App\Helper\DTO\CommentsDTO;
use App\Repository\CommentsRepository;
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
 * @OA\Tag(name="Comments")
 */
#[Route('/comments')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class CommentsApiController extends AbstractController
{

    /**
     * Добавить комментарий продукту
     *
     * @OA\RequestBody(
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             ref=@Model(type="App\Helper\DTO\CommentsDTO", groups={"create_comments"})
     *         )
     *
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
     * @OA\Parameter(
     *     name="product",
     *     in="path",
     *     description="id - product",
     *     @OA\Schema(type="integer")
     * )
     */
//    #[Route('/{product<\d+>}', name: 'create_comments', methods: ['GET'])]
    public function getProduct(
        Products $product,
        Request $request,
        SerializerInterface $serializer,
        ValidatorService $validatorService,
        CommentsRepository $commentsRepository,
    ): JsonResponse
    {
        /** @var CommentsDTO $commentsDTO */
        $commentsDTO = $serializer->deserialize(
            json_encode($request->request->all()),
            CommentsDTO::class,
            'json'
        );
        $validatorService->validate($commentsDTO, ['create_comments']);
        $comments = (new Comments())
            ->setRating($commentsDTO->getRating())
            ->setText($commentsDTO->getText());

        $files = $request->files->all();
        $commentsRepository->save($comments, true);
        return $this->json(data: [], status: Response::HTTP_NO_CONTENT);
    }
}