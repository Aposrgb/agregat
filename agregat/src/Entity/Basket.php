<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_basket', 'get_baskets'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'baskets')]
    #[Groups(['get_basket', 'get_baskets'])]
    private ?Products $product = null;

    #[ORM\Column]
    #[Groups(['get_basket', 'get_baskets'])]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'baskets')]
    #[Groups(['get_basket', 'get_baskets'])]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
