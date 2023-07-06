<?php

namespace App\Helper\DTO\Response;

use App\Entity\Comments;

readonly class CommentResponse
{
    public ?int $id;
    public ?string $text;
    public ?int $rating;
    /** @var string[] $images */
    public array $images;
    public ?UserResponse $owner;

    public function __construct(Comments $comments)
    {
        $this->id = $comments->getId();
        $this->text = $comments->getText();
        $this->rating = $comments->getRating();
        $this->images = $comments->getImages();
        $this->owner = $comments->getOwner() ? new UserResponse($comments->getOwner()) : null;
    }

}