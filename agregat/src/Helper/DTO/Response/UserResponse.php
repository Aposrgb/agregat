<?php

namespace App\Helper\DTO\Response;

use App\Entity\User;

readonly class UserResponse
{
    public ?int $id;
    public ?string $surname;
    public ?string $firstname;
    public ?string $patronymic;
    public ?string $email;
    public ?string $photo;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->surname = $user->getSurname();
        $this->firstname = $user->getFirstname();
        $this->patronymic = $user->getPatronymic();
        $this->email = $user->getEmail();
        $this->photo = $user->getPhoto();
    }


}