<?php

namespace App\Helper\DTO;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    /** @OA\Property(type="integer") */
    #[Groups(groups: ['create_purchase'])]
    #[Assert\NotBlank(groups: ['create_purchase'])]
    #[Assert\Type(type: 'integer', groups: ['create_purchase'])]
    #[Assert\Positive(groups: ['create_purchase'])]
    protected $count = null;

    /** @OA\Property(type="integer") */
    #[Groups(groups: ['create_purchase'])]
    #[Assert\NotBlank(groups: ['create_purchase'])]
    #[Assert\Type(type: 'integer', groups: ['create_purchase'])]
    #[Assert\Positive(groups: ['create_purchase'])]
    protected $productId = null;

    /**
     * @return null
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param null $count
     * @return ProductDTO
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return null
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param null $productId
     * @return ProductDTO
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }


}