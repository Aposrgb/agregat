<?php

namespace App\Controller\Api;

use App\Entity\Feedback;
use App\Helper\DTO\FeedbackDTO;
use App\Repository\FeedbackRepository;
use App\Service\MailerService;
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
 * @OA\Tag(name="Feedback")
 */
#[Route('/feedback')]
class FeedbackApiController extends AbstractController
{
    /**
     * Создание обратной связи
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(ref=@Model(type="App\Helper\DTO\FeedbackDTO", groups={"create_feedback"}))
     * )
     *
     * @OA\Response(
     *     response="400",
     *     description="Not valid data",
     *     @OA\JsonContent(ref="#/components/schemas/ApiException")
     * )
     *
     * @OA\Response(
     *     response="201",
     *     description="success",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\Feedback", groups={"get_feedback"})
     *          )
     *     )
     * )
     *
     */
    #[Route('', name: 'create_feedback', methods: ['POST'])]
    public function createFeedback(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorService    $validatorService,
        FeedbackRepository  $feedbackRepository,
        MailerService       $mailerService,
    ): JsonResponse
    {
        /** @var FeedbackDTO $feedbackDTO */
        $feedbackDTO = $serializer->deserialize(
            $request->getContent(), FeedbackDTO::class, 'json'
        );
        $validatorService->validate($feedbackDTO, ['create_feedback']);
        $feedback = (new Feedback())
            ->setEmail($feedbackDTO->getEmail())
            ->setName($feedbackDTO->getName())
            ->setPhone($feedbackDTO->getPhone())
            ->setMessage($feedbackDTO->getMessage());

        $feedbackRepository->save($feedback, true);
        $mailerService->sendMailTemplate('mail/mailer.html.twig','Обратная связь АгрегатЕКБ', context: [
            'feedBack' => $feedback
        ]);
        return $this->json(
            data: [
                "data" => $feedback,
            ],
            status: Response::HTTP_CREATED,
            context: ['groups' => ['get_feedback']]

        );
    }
}