<?php

namespace App\Helper\Mapper;

use App\Entity\User;
use App\Helper\DTO\UserDTO;
use App\Helper\Interface\MapperInterface;
use App\Service\HelperService;

class UserMapper implements MapperInterface
{
    public function __construct(
        protected HelperService $helperService
    )
    {
    }

    /**
     * @param UserDTO $dto
     * @param User $entity
     */
    public function dtoToEntity($dto, $entity = null)
    {
        $entity = $entity ?? new User();
        return $entity
            ->setIsJuristic($this->helperService->getActualValueBool($entity->getIsJuristic(), $dto->getIsJuristic()))
            ->setFirstname($this->helperService->getActualValue($entity->getFirstName(), $dto->getName()))
            ->setSurname($this->helperService->getActualValue($entity->getSurname(), $dto->getSurname()))
            ->setPatronymic($this->helperService->getActualValue($entity->getPatronymic(), $dto->getPatronymic()))
            ->setPhone($this->helperService->getActualValue($entity->getPhone(), $dto->getPhone()))
            ->setEmail($this->helperService->getActualValue($entity->getEmail(), $dto->getEmail()))
            ->setSurname($this->helperService->getActualValue($entity->getSurname(), $dto->getSurname()))
            ->setCountry($this->helperService->getActualValue($entity->getCountry(), $dto->getCountry()))
            ->setCity($this->helperService->getActualValue($entity->getCity(), $dto->getCity()))
            ->setLocality($this->helperService->getActualValue($entity->getLocality(), $dto->getLocality()))
            ->setIndex($this->helperService->getActualValue($entity->getIndex(), $dto->getIndex()))
            ->setAddress($this->helperService->getActualValue($entity->getAddress(), $dto->getAddress()))
            ->setDateBirth($this->helperService->getActualValueDate($entity->getDateBirth(), $dto->getDateBirth()));
    }

    public function entityToDTO($entity)
    {
        // TODO: Implement entityToDTO() method.
    }

}