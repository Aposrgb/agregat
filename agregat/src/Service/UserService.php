<?php

namespace App\Service;

use App\Entity\User;
use App\Helper\Exception\ApiException;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class UserService
{
    const PATH_PHOTO = "/upload/user/photo/";

    const AVAILABLE_IMAGE_EXTENSIONS = [
        'image/jpg',
        'image/jpeg',
        'image/png',
    ];

    public function __construct(
        protected UserRepository $userRepository,
    )
    {
    }

    public function getUserById(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new ApiException(message: 'Пользователь не найден', status: Response::HTTP_NOT_FOUND);
        }
        return $user;
    }

    public function checkEmailExist(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user) {
            throw new ApiException(message: 'Уже есть пользователь с такой почтой');
        }
    }
}