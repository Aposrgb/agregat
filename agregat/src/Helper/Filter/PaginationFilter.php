<?php

namespace App\Helper\Filter;

use App\Helper\Mapped\Helper;
use App\Service\ValidatorService;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PaginationFilter
{
    #[Assert\Range(min: 1, max: Helper::MAX_SIZE_INTEGER, groups: ['pagination', 'filter', 'get_skills'])]
    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['pagination', 'filter', 'get_skills'])]
    #[Assert\Positive(groups: ['pagination', 'filter', 'get_skills'])]
    #[Groups(groups: ['pagination', 'filter', 'get_skills'])]
    protected string|int $page = 1;

    #[Assert\Range(min: 1, max: Helper::MAX_SIZE_INTEGER, groups: ['pagination', 'filter', 'get_skills'])]
    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['pagination', 'filter', 'get_skills'])]
    #[Assert\Positive( groups: ['pagination', 'filter', 'get_skills'])]
    #[Groups(groups: ['pagination', 'filter', 'get_skills'])]
    protected string|int $limit = 10;

    public function getFirstMaxResult()
    {
        return $this->getLimit() * ($this->getPage() - 1);
    }

    public function getPage(): string|int
    {
        return $this->page;
    }

    public function setPage(string|int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit(): string|int
    {
        return $this->limit;
    }

    public function setLimit(string|int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

}