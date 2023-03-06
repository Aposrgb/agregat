<?php

namespace App\Controller\Api;

use App\Helper\Exception\ApiException;
use App\Service\MailerService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Call")
 *
 */
#[Route('/call')]
class CallApiController extends AbstractController
{
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
    #[Route('/{phone}', name: 'send_order', methods: ['PATCH'])]
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
}