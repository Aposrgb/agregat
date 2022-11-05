<?php

namespace App\Helper\Filter;

use App\Service\ValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

class ProductsFilter
{
    #[Assert\Valid(groups: ['filter'])]
    private ?PaginationFilter $pagination;

    private $isNew;

    private $isPopular;

    private $isAvailable;

    private $isRecommend;

    private $isActual;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['filter'])]
    private $categoryId;

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
    public function getIsNew(): ?bool
    {
        return $this->isNew;
    }

    /**
     * @param mixed $isNew
     * @return ProductsFilter
     */
    public function setIsNew(string $isNew): self
    {
        $this->isNew = $isNew == "true";
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPopular(): ?bool
    {
        return $this->isPopular;
    }

    /**
     * @param mixed $isPopular
     * @return ProductsFilter
     */
    public function setIsPopular(string $isPopular): self
    {
        $this->isPopular = $isPopular == "true";
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    /**
     * @param mixed $isAvailable
     * @return ProductsFilter
     */
    public function setIsAvailable(string $isAvailable): self
    {
        $this->isAvailable = $isAvailable == "true";
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsRecommend(): ?bool
    {
        return $this->isRecommend;
    }

    /**
     * @param mixed $isRecommend
     * @return ProductsFilter
     */
    public function setIsRecommend(string $isRecommend): self
    {
        $this->isRecommend = $isRecommend == "true";
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
}