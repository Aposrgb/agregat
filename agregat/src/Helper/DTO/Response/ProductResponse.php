<?php

namespace App\Helper\DTO\Response;

use App\Entity\Comments;
use App\Entity\Products;

readonly class ProductResponse
{
    public ?int $id;

    public ?string $title;

    public ?string $img;

    public ?float $rating;

    public ?string $description;

    public ?bool $isActual;

    public ?\DateTimeInterface $createdAt;

    public ?CategoriesResponse $categories;

    public ?float $price;

    public ?float $discountPrice;

    public ?string $code1C;

    public ?int $balanceStock;

    public ?string $keyWords;

    /** @var CommentResponse[] $comments */
    public array $comments;

    public ?string $article;

    public function __construct(Products $products, int $price)
    {
        $this->id = $products->getId();
        $this->title = $products->getTitle();
        $this->price = $price;
        $this->img = $products->getImg();
        $this->rating = $products->getAverageRating();
        $this->description = $products->getDescription();
        $this->isActual = $products->isIsActual();
        $this->createdAt = $products->getCreatedAt();
        $this->categories = $products->getCategories() ? new CategoriesResponse($products->getCategories()) :null;
        $this->discountPrice = $products->getDiscountPrice();
        $this->code1C = $products->getCode1C();
        $this->balanceStock = $products->getBalanceStock();
        $this->keyWords = $products->getKeyWords();
        $this->comments = $products
            ->getComments()
            ->map(fn(Comments $comments) => new CommentResponse($comments))
            ->getValues();
    }



}