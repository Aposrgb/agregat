<?php

namespace App\Controller\Api;

use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="User")
 *
 * @OA\Response(
 *     response="401",
 *     description="Unauthorized",
 *     @OA\JsonContent(ref="#/components/schemas/ApiException")
 * )
 *
 */
#[Route('/user')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserApiController  extends AbstractController
{

    /**
     * Получения профиля
     *
     * @OA\Response(
     *     response="200",
     *     description="Ok",
     *     @OA\JsonContent(
     *         @OA\Property(property="data", type="object",
     *              ref=@Model(type="App\Entity\User", groups={"get_profile"})
     *         )
     *     )
     * )
     *
     */
    #[Route('', name: 'get_profile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        return $this->json(
            data: ['data' => $this->getUser()],
            context: ['groups' => ['get_profile']]
        );
    }
}