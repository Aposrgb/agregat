<?php

namespace App\Helper\Filter;

use App\Service\ValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

class NewsFilter
{
    #[Assert\Valid(groups: ['filter'])]
    private ?PaginationFilter $pagination;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateYearContext'], groups: ['filter'])]
    private $year;

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
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     * @return NewsFilter
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

}