<?php

namespace App\Helper\Mapped;

use App\Entity\Brand;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class ProductFilter
{
    /** @OA\Property(type="array", @OA\Items(ref=@Model(type="App\Entity\Categories", groups={"get_filter"}))) */
    #[Groups(['get_filter'])]
    private array $categories = [];

    /** @OA\Property(type="array", @OA\Items(ref=@Model(type="App\Entity\SubCategories", groups={"get_filter"}))) */
    #[Groups(['get_filter'])]
    private array $subCategories = [];

    /** @OA\Property(type="array", @OA\Items(ref=@Model(type="App\Entity\Brand", groups={"get_filter"}))) */
    #[Groups(['get_filter'])]
    private array $brands = [];

    #[Groups(['get_filter'])]
    private ?int $minPrice = 0;

    #[Groups(['get_filter'])]
    private ?int $maxPrice = 0;

    #[Groups(['get_filter'])]
    private ?float $maxRating = 0;

    #[Groups(['get_filter'])]
    private ?float $minRating = 0;

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return ProductFilter
     */
    public function setCategories(array $categories): ProductFilter
    {
        $this->categories = $categories;
        return $this;
    }

    public function addCategory($category): self
    {
        if (!in_array($category, $this->categories)) {
            $this->categories[] = $category;
        }
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }

    /**
     * @param int|null $minPrice
     * @return ProductFilter
     */
    public function setMinPrice(?int $minPrice): ProductFilter
    {
        $this->minPrice = $minPrice;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    /**
     * @param int|null $maxPrice
     * @return ProductFilter
     */
    public function setMaxPrice(?int $maxPrice): ProductFilter
    {
        $this->maxPrice = $maxPrice;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubCategories(): array
    {
        return $this->subCategories;
    }

    /**
     * @param array $subCategories
     * @return ProductFilter
     */
    public function setSubCategories(array $subCategories): ProductFilter
    {
        $this->subCategories = $subCategories;
        return $this;
    }

    public function addSubCategory($category): self
    {
        if (!in_array($category, $this->subCategories)) {
            $this->subCategories[] = $category;
        }
        return $this;
    }

    public function getMinRating(): ?float
    {
        return $this->minRating;
    }


    public function setMinRating(?float $minRating): ProductFilter
    {
        $this->minRating = $minRating;
        return $this;
    }

    public function getMaxRating(): ?float
    {
        return $this->maxRating;
    }

    public function setMaxRating(?float $maxRating): ProductFilter
    {
        $this->maxRating = $maxRating;
        return $this;
    }

    public function addBrand(Brand $brand): self
    {
        if (!in_array($brand, $this->brands)) {
            $this->brands[] = $brand;
        }
        return $this;
    }

    public function getBrands(): array
    {
        return $this->brands;
    }

    public function setBrands(array $brands): ProductFilter
    {
        $this->brands = $brands;
        return $this;
    }
}