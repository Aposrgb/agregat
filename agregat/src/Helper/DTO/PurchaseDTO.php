<?php

namespace App\Helper\DTO;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseDTO
{
    /** @OA\Property(type="integer", description="Кол-во продуктов") */
    #[Groups(['create_purchase'])]
    #[Assert\Positive(groups: ['create_purchase'])]
    #[Assert\NotBlank(groups: ['create_purchase'])]
    #[Assert\Type(type: 'integer', groups: ['create_purchase'])]
    private $count = null;

    /** @OA\Property(type="string", description="Имя") */
    #[Groups(['create_purchase'])]
    #[Assert\NotBlank(groups: ['create_purchase'])]
    #[Assert\Type(type: 'string', groups: ['create_purchase'])]
    private $name = null;

    /** @OA\Property(type="string", description="Фамилия") */
    #[Groups(['create_purchase'])]
    #[Assert\NotBlank(groups: ['create_purchase'])]
    #[Assert\Type(type: 'string', groups: ['create_purchase'])]
    private $surname = null;

    /** @OA\Property(type="string", description="Телефон") */
    #[Groups(['create_purchase'])]
    #[Assert\NotBlank(groups: ['create_purchase'])]
    #[Assert\Type(type: 'string', groups: ['create_purchase'])]
    private $phone = null;

    /** @OA\Property(type="string", description="Адрес") */
    #[Groups(['create_purchase'])]
    #[Assert\NotBlank(groups: ['create_purchase'])]
    #[Assert\Type(type: 'string', groups: ['create_purchase'])]
    private $address = null;

    public function getCount(): mixed
    {
        return $this->count;
    }

    public function setCount($count): self
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     * @return PurchaseDTO
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param null $surname
     * @return PurchaseDTO
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param null $phone
     * @return PurchaseDTO
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param null $address
     * @return PurchaseDTO
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

}