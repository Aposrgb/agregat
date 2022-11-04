<?php

namespace App\Helper\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class ProductsFilter
{
    #[Assert\Valid(groups: ['filter'])]
    protected ?PaginationFilter $pagination;

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
}