<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Helper\DTO\UserDTO;
use App\Helper\Exception\ApiException;
use App\Helper\Mapper\UserMapper;
use App\Service\FileUploadService;
use App\Service\UserService;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;

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
class UserApiController extends AbstractController
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

    /**
     * Редактирование профиля
     *
     * @OA\RequestBody(
     *     description="Только поля которые нужно сменить",
     *     @OA\JsonContent(
     *         ref=@Model(type="App\Helper\DTO\UserDTO", groups={"edit_user"})
     *     )
     * )
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
    #[Route('', name: 'edit_user', methods: ['PATCH'])]
    public function editProfile(
        Request                $request,
        SerializerInterface    $serializer,
        ValidatorService       $validator,
        UserMapper             $userMapper,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        $userDTO = $serializer->deserialize($request->getContent(), UserDTO::class, 'json');
        $validator->validate($userDTO, ['edit_user']);
        $user = $userMapper->dtoToEntity($userDTO, $this->getUser());
        $entityManager->flush();
        return $this->json(
            data: ['data' => $user],
            context: ['groups' => ['get_profile']]
        );
    }

    /**
     * Загрузка фото для пользователя
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *             @OA\Property(property="img", type="file")
     *         )
     *     )
     * )
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
    #[Route('/photo', name: 'edit_user_photo', methods: ['POST'])]
    public function uploadPhoto(
        Request                $request,
        FileUploadService      $fileUploadService,
        ValidatorService       $validatorService,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        $file = $request->files->get('img');
        if (!$file) {
            throw new ApiException(message: 'Нет изображения');
        }
        $validatorService->validateImagesExtension([$file], UserService::AVAILABLE_IMAGE_EXTENSIONS);
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getPhoto()) {
            $fileUploadService->deleteFile($user->getPhoto());
        }
        $user->setPhoto($fileUploadService->upload($file, UserService::PATH_PHOTO));
        $entityManager->flush();
        return $this->json(
            data: ['data' => $user],
            context: ['groups' => ['get_profile']]
        );
    }
}