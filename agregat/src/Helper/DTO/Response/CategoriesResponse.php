<?php

namespace App\Helper\DTO\Response;

use App\Entity\Categories;

readonly class CategoriesResponse
{
    public ?int $id;

    public ?bool $isPopular;

    public ?string $img;

    public ?string $title;

    public ?string $description;

    public function __construct(Categories $categories)
    {
        $this->id = $categories->getId();
        $this->isPopular = $categories->isIsPopular();
        $this->img = $categories->getImg();
        $this->title = $categories->getTitle();
        $this->description = $categories->getDescription();
    }

}