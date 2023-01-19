<?php

namespace App\Entity;

use App\Helper\EnumStatus\DeliveryStatus;
use App\Helper\EnumStatus\PurchaseStatus;
use App\Helper\EnumType\PurchaseAddressType;
use App\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_purchase', 'get_purchase_user'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['get_purchase', 'get_purchase_user'])]
    private ?\DateTimeInterface $datePurchase = null;

    #[ORM\Column]
    #[Groups(['get_purchase_user'])]
    private ?int $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['get_purchase_user'])]
    private ?\DateTimeInterface $dateArrive = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['get_purchase_user'])]
    private ?int $deliveryService = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[Groups(['get_purchase_user'])]
    private ?Products $product = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[Groups(['get_purchase_user'])]
    private ?User $owner = null;

    #[ORM\Column]
    #[Groups(['get_purchase', 'get_purchase_user'])]
    private ?int $count = null;

    #[ORM\Column]
    #[Groups(['get_purchase', 'get_purchase_user'])]
    private ?int $price = null;

    #[ORM\Column]
    #[Groups(['get_purchase_user'])]
    private ?int $deliveryStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_purchase_user'])]
    private ?string $deliveryAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_purchase_user'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_purchase_user'])]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_purchase_user'])]
    private ?string $phone = null;

    public function __construct()
    {
        $this->datePurchase = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePurchase(): ?\DateTimeInterface
    {
        return $this->datePurchase;
    }

    public function setDatePurchase(\DateTimeInterface $datePurchase): self
    {
        $this->datePurchase = $datePurchase;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDateArrive(): ?\DateTimeInterface
    {
        return $this->dateArrive;
    }

    public function setDateArrive(?\DateTimeInterface $dateArrive): self
    {
        $this->dateArrive = $dateArrive;

        return $this;
    }

    public function getDeliveryService(): ?int
    {
        return $this->deliveryService;
    }

    public function setDeliveryService(?int $deliveryService): self
    {
        $this->deliveryService = $deliveryService;

        return $this;
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDeliveryStatus(): ?int
    {
        return $this->deliveryStatus;
    }

    public function setDeliveryStatus(int $deliveryStatus): self
    {
        $this->deliveryStatus = $deliveryStatus;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    #[Groups(['get_purchase_user'])]
    public function getPurchaseStatusName(): ?string
    {
        return PurchaseStatus::tryFrom($this->status)?->getTypeName();
    }

    #[Groups(['get_purchase_user'])]
    public function getDeliveryStatusName(): ?string
    {
        return DeliveryStatus::tryFrom($this->deliveryStatus)?->getTypeName();
    }

    #[Groups(['get_purchase_user'])]
    public function getDeliveryServiceName(): ?string
    {
        return PurchaseAddressType::tryFrom($this->deliveryService)?->getTypeName();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
