<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_products', 'get_product_detail', 'get_basket', 'get_baskets', 'get_comments', 'get_purchase_user'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets', 'get_comments', 'get_purchase_user'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets', 'get_purchase_user'])]
    private ?string $img = null;

    #[ORM\Column]
    private ?float $rating = 0.0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    private ?bool $isActual = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['get_products'])]
    private ?\DateTimeInterface $createdAt;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    private ?Categories $categories = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    private ?float $discountPrice = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Basket::class, cascade: ['remove'])]
    private Collection $baskets;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'favoritesProducts')]
    private Collection $favoritesUser;

    #[ORM\Column(nullable: true)]
    private ?string $code1C = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    private ?int $balanceStock = null;

    #[ORM\Column(nullable: true)]
    private ?int $purchaseBalance = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    private ?string $keyWords = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?SubCategories $subCategories = null;

    /**
     * @OA\Property(type="array",
     *      @OA\Items(
     *          @OA\Property(property="id", type="integer"),
     *          @OA\Property(property="name", type="string")
     *      )
     * )
     */
    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $bundle = [];

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Comments::class, cascade: ['remove'])]
    #[Groups(['get_product_detail'])]
    private Collection $comments;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'products')]
    private ?Brand $brand = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Purchase::class, cascade: ['remove'])]
    private Collection $purchases;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    private ?string $article = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->baskets = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->favoritesUser = new ArrayCollection();
        $this->purchases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getDescription(): ?string
    {
        if ($this->article) {
            return $this->description . "\nАртикул: " . $this->article;
        }
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isIsActual(): ?bool
    {
        return $this->isActual;
    }

    public function setIsActual(bool $isActual): self
    {
        $this->isActual = $isActual;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return Products
     */
    public function setPrice(?float $price): Products
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getDiscountPrice(): ?float
    {
        return $this->discountPrice;
    }

    /**
     * @param float|null $discountPrice
     * @return Products
     */
    public function setDiscountPrice(?float $discountPrice): Products
    {
        $this->discountPrice = $discountPrice;
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
            $basket->setProduct($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): self
    {
        if ($this->baskets->removeElement($basket)) {
            // set the owning side to null (unless already changed)
            if ($basket->getProduct() === $this) {
                $basket->setProduct(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, User> */
    public function getFavoritesUser(): ?Collection
    {
        return $this->favoritesUser;
    }

    public function addFavoritesUser(User $favoritesUser): self
    {
        if (!$this->favoritesUser->contains($favoritesUser)) {
            $this->favoritesUser[] = $favoritesUser;
        }
        return $this;
    }

    public function removeFavoritesUser(User $favoritesUser): self
    {
        $this->favoritesUser->removeElement($favoritesUser);
        return $this;
    }

    public function setFavoritesUser(?Collection $favoritesUser): self
    {
        $this->favoritesUser = $favoritesUser;

        return $this;
    }

    public function getCode1C(): ?string
    {
        return $this->code1C;
    }

    public function setCode1C(?string $code1C): self
    {
        $this->code1C = $code1C;

        return $this;
    }

    public function getBalanceStock(): ?int
    {
        return $this->balanceStock;
    }

    public function setBalanceStock(?int $balanceStock): self
    {
        $this->balanceStock = $balanceStock;

        return $this;
    }

    public function getPurchaseBalance(): ?int
    {
        return $this->purchaseBalance;
    }

    public function setPurchaseBalance(?int $purchaseBalance): self
    {
        $this->purchaseBalance = $purchaseBalance;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeyWords(): ?string
    {
        return $this->keyWords;
    }

    /**
     * @param string|null $keyWords
     * @return Products
     */
    public function setKeyWords(?string $keyWords): Products
    {
        $this->keyWords = $keyWords;
        return $this;
    }

    public function getSubCategories(): ?SubCategories
    {
        return $this->subCategories;
    }

    public function setSubCategories(?SubCategories $subCategories): self
    {
        $this->subCategories = $subCategories;

        return $this;
    }

    public function getBundle(): ?array
    {
        return $this->bundle;
    }

    public function setBundle(?array $bundle): self
    {
        $this->bundle = $bundle;

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
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }

    #[Groups(['get_products', 'get_product_detail', 'get_baskets'])]
    #[SerializedName(serializedName: 'rating')]
    public function getAverageRating(): ?float
    {
        $count = $this->comments->count();
        $rating = 0;
        foreach ($this->comments as $comment) {
            $rating += $comment->getRating();
        }
        return $count > 0 ? $rating / $count : 0;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

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
            $purchase->setProduct($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getProduct() === $this) {
                $purchase->setProduct(null);
            }
        }

        return $this;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(?string $article): self
    {
        $this->article = $article;

        return $this;
    }

}
