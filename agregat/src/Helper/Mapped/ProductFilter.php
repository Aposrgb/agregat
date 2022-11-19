<?php

namespace App\Helper\Mapped;

class ProductFilter
{
    private array $categories = [];

    private array $subCategories = [];

    private ?int $minPrice = 0;

    private ?int $maxPrice = 0;

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
}