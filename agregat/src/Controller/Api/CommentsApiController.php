<?php

namespace App\Controller\Api;

use App\Entity\Comments;
use App\Entity\Products;
use App\Helper\DTO\CommentsDTO;
use App\Helper\Exception\ApiException;
use App\Repository\CommentsRepository;
use App\Service\CommentsService;
use App\Service\FileUploadService;
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
     *     description="Можно оставить только 2 комментария на продукт",
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
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="data", type="object",
     *             ref=@Model(type="App\Entity\Comments", groups={"get_comments"})
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
    #[Route('/{product<\d+>}', name: 'create_comments', methods: ['POST'])]
    public function addComment(
        Products            $product,
        Request             $request,
        SerializerInterface $serializer,
        ValidatorService    $validatorService,
        CommentsRepository  $commentsRepository,
        FileUploadService   $fileUploadService,
        CommentsService     $commentsService,
    ): JsonResponse
    {
        /** @var CommentsDTO $commentsDTO */
        $commentsDTO = $serializer->deserialize(
            json_encode($request->request->all()),
            CommentsDTO::class,
            'json'
        );
        $validatorService->validate($commentsDTO, ['create_comments']);
        $commentsService->checkUserComments($this->getUser(), $product);
        $files = $request->files->all();
        $validatorService->validateImagesExtension($files, CommentsService::AVAILABLE_IMAGE_EXTENSIONS);
        if (count($files) > CommentsService::COUNT_IMG_COMMENTS) {
            throw new ApiException(message: 'Кол-во вложений превышено');
        }
        $images = [];
        foreach ($files as $file) {
            $images[] = $fileUploadService->upload($file, CommentsService::PATH_TO_IMG);
        }
        $commentsRepository->save($comments =
            (new Comments())
                ->setProduct($product)
                ->setOwner($this->getUser())
                ->setRating($commentsDTO->getRating())
                ->setText($commentsDTO->getText())
                ->setImages($images), true
        );
        return $this->json(
            data: ['data' => $comments],
            status: Response::HTTP_CREATED,
            context: ['groups' => ['get_comments']]);
    }
}