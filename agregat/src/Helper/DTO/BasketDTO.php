<?php

namespace App\Helper\DTO;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class BasketDTO
{
    /** @OA\Property(type="integer") */
    #[Assert\NotBlank(groups: ['add_basket', 'edit_count'])]
    #[Assert\Positive(groups: ['add_basket', 'edit_count'])]
    #[Assert\Type(type: 'integer', groups: ['add_basket', 'edit_count'])]
    #[Groups(groups: ['add_basket', 'edit_count'])]
    private $count;

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     * @return BasketDTO
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }
}