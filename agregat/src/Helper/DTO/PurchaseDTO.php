<?php

namespace App\Helper\DTO;

use App\Helper\EnumType\PurchaseAddressType;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseDTO
{
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
    #[Assert\Type(type: 'string', groups: ['create_purchase'])]
    private $address = null;

    /** @OA\Property(type="integer", description="Тип доставки - 1 - Пункт Агрегат ЕКБ, 2 - Почта России, 3 - Самовывоз, 4 - Доставка") */
    #[Groups(['create_purchase'])]
    #[Assert\NotBlank(groups: ['create_purchase'])]
    #[Assert\Choice(callback: [PurchaseAddressType::class, 'getTypes'], groups: ['create_purchase'])]
    #[Assert\Type(type: 'integer', groups: ['create_purchase'])]
    private $deliveryService = null;

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

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param array $products
     * @return PurchaseDTO
     */
    public function setProducts(array $products): PurchaseDTO
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @return null
     */
    public function getDeliveryService()
    {
        return $this->deliveryService;
    }

    /**
     * @param null $deliveryService
     * @return PurchaseDTO
     */
    public function setDeliveryService($deliveryService)
    {
        $this->deliveryService = $deliveryService;
        return $this;
    }

}