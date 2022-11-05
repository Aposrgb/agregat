<?php

namespace App\Helper\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class CategoriesFilter
{
    #[Assert\Valid(groups: ['filter'])]
    private ?PaginationFilter $pagination;

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