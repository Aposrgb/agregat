<?php

namespace App\Controller\Api;

use App\Helper\EnumType\TextsType;
use App\Repository\TextsRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Texts")
 */
#[Route('/texts')]
class TextsApiController extends AbstractController
{
    /**
     * Получение текстов для контактов
     *
     * subTypes (ADDRESS = 1, EMAIL = 2, PHONE = 3, WORK_TIME  = 4, ABOUT_COMPANY = 5)
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="array",
     *              @OA\Items(ref=@Model(type="App\Entity\Texts", groups={"get_texts_contact"}))
     *          )
     *     )
     * )
     *
     */
    #[Route('/contacts', name: 'get_texts_contact', methods: ['GET'])]
    public function getTextsContact(
        TextsRepository $textsRepository
    ): JsonResponse
    {
        return $this->json(
            data: ['data' => $textsRepository->findBy(['type' => TextsType::CONTACTS->value])],
            context: ['groups' => ['get_texts_contact']]
        );
    }
    /**
     * Получение текстов для как купить
     *
     * subTypes (Условия доставки = 6, Условия оплаты = 7, Гарантии на товар = 8)
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *          @OA\Property(property="data", type="array",
     *              @OA\Items(ref=@Model(type="App\Entity\Texts", groups={"get_texts"}))
     *          )
     *     )
     * )
     *
     */
    #[Route('/how-buy', name: 'get_texts_how_buy', methods: ['GET'])]
    public function getTextsHowBuy(
        TextsRepository $textsRepository
    ): JsonResponse
    {
        return $this->json(
            data: ['data' => $textsRepository->findBy(['type' => TextsType::HOW_TO_BUY->value])],
            context: ['groups' => ['get_texts']]
        );
    }
}