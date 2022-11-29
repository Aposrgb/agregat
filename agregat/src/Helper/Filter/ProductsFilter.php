<?php

namespace App\Helper\Filter;

use App\Service\ValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

class ProductsFilter
{
    #[Assert\Valid(groups: ['filter'])]
    private ?PaginationFilter $pagination;

    private $isActual;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateArrayInteger'], groups: ['filter'])]
    private $categoryId;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateArrayInteger'], groups: ['filter'])]
    private $subCategoryId;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['filter'])]
    private $minPrice;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['filter'])]
    private $maxPrice;

    private $name;

    public function __construct()
    {
        $this->pagination = new PaginationFilter();
    }

    public function getPagination(): PaginationFilter
    {
        return $this->pagination;
    }

    public function setPagination(PaginationFilter $pagination): self
    {
        $this->pagination = $pagination;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsActual(): ?bool
    {
        return $this->isActual;
    }

    /**
     * @param mixed $isActual
     * @return ProductsFilter
     */
    public function setIsActual(string $isActual): self
    {
        $this->isActual = $isActual == "true";
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param mixed $categoryId
     * @return ProductsFilter
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinPrice()
    {
        return $this->minPrice;
    }

    /**
     * @param mixed $minPrice
     * @return ProductsFilter
     */
    public function setMinPrice($minPrice)
    {
        $this->minPrice = $minPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * @param mixed $maxPrice
     * @return ProductsFilter
     */
    public function setMaxPrice($maxPrice)
    {
        $this->maxPrice = $maxPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubCategoryId()
    {
        return $this->subCategoryId;
    }

    /**
     * @param mixed $subCategoryId
     * @return ProductsFilter
     */
    public function setSubCategoryId($subCategoryId)
    {
        $this->subCategoryId = $subCategoryId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return ProductsFilter
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

}