<?php

namespace App\Entity;

use App\Helper\EnumStatus\UserStatus;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['get_basket', 'get_baskets', 'get_comments', 'get_profile', 'get_purchase_user', 'get_product_detail'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['get_profile', 'get_purchase_user', 'get_product_detail'])]
    private ?string $surname = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['get_profile'])]
    private ?\DateTimeInterface $dateRegistration;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['get_profile', 'get_purchase_user', 'get_product_detail'])]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['get_profile', 'get_product_detail'])]
    private ?string $patronymic = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['get_profile', 'get_product_detail'])]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['get_profile', 'get_purchase_user'])]
    private ?string $phone = null;

    #[ORM\Column(type: 'simple_array')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(type: 'integer')]
    private ?int $status;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $password;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Device::class, cascade: ['persist'])]
    private Collection $devices;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Basket::class)]
    private Collection $baskets;

    #[ORM\ManyToMany(targetEntity: Products::class, mappedBy: 'favoritesUser')]
    private Collection $favoritesProducts;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Comments::class)]
    private Collection $comments;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['get_profile'])]
    private ?\DateTimeInterface $dateBirth = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_profile'])]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_profile'])]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_profile'])]
    private ?string $locality = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_profile'])]
    private ?string $index = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_profile'])]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Purchase::class)]
    private Collection $purchases;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_profile', 'get_purchase_user', 'get_product_detail'])]
    private ?string $photo = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isJuristic = null;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
        $this->baskets = new ArrayCollection();
        $this->dateRegistration = new \DateTime();
        $this->status = UserStatus::CONFIRMED->value;
        $this->favoritesProducts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->purchases = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return User
     */
    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     * @return User
     */
    public function setSurname(?string $surname): User
    {
        $this->surname = $surname;
        return $this;
    }

    public function getDateRegistration(): ?\DateTimeInterface
    {
        return $this->dateRegistration;
    }

    public function setDateRegistration(?\DateTimeInterface $dateRegistration): self
    {
        $this->dateRegistration = $dateRegistration;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return User
     */
    public function setFirstname(?string $firstname): User
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    /**
     * @param string|null $patronymic
     * @return User
     */
    public function setPatronymic(?string $patronymic): User
    {
        $this->patronymic = $patronymic;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return User
     */
    public function setEmail(?string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return User
     */
    public function setPhone(?string $phone): User
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     * @return User
     */
    public function setStatus(?int $status): User
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return User
     */
    public function setPassword(?string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setOwner($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getOwner() === $this) {
                $device->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Basket>
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Basket $basket): self
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets->add($basket);
            $basket->setOwner($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): self
    {
        if ($this->baskets->removeElement($basket)) {
            // set the owning side to null (unless already changed)
            if ($basket->getOwner() === $this) {
                $basket->setOwner(null);
            }
        }

        return $this;
    }

    public function eraseCredentials()
    {

    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getFavoritesProducts(): Collection
    {
        return $this->favoritesProducts;
    }

    public function addFavoritesProduct(Products $favoritesProduct): self
    {
        if (!$this->favoritesProducts->contains($favoritesProduct)) {
            $this->favoritesProducts[] = $favoritesProduct;
            $favoritesProduct->addFavoritesUser($this);
        }

        return $this;
    }

    public function removeFavoritesProduct(Products $favoritesProduct): self
    {
        $this->favoritesProducts->removeElement($favoritesProduct);
        $favoritesProduct->removeFavoritesUser($this);

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setOwner($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getOwner() === $this) {
                $comment->setOwner(null);
            }
        }

        return $this;
    }

    public function getDateBirth(): ?\DateTimeInterface
    {
        return $this->dateBirth;
    }

    public function setDateBirth(?\DateTimeInterface $dateBirth): self
    {
        $this->dateBirth = $dateBirth;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(?string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }

    public function setIndex(?string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setOwner($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getOwner() === $this) {
                $purchase->setOwner(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsJuristic(): ?bool
    {
        return $this->isJuristic;
    }

    /**
     * @param bool|null $isJuristic
     * @return User
     */
    public function setIsJuristic(?bool $isJuristic): User
    {
        $this->isJuristic = $isJuristic;
        return $this;
    }

}
