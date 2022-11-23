<?php

namespace App\Service;

use App\Entity\Products;
use App\Entity\User;
use App\Helper\Exception\ApiException;

class CommentsService
{
    const PATH_TO_IMG = '/upload/comments/';
    const LIMIT_COMMENTS_USER = 2;
    const COUNT_IMG_COMMENTS = 5;

    const AVAILABLE_IMAGE_EXTENSIONS = [
        'image/jpg',
        'image/jpeg',
        'image/png',
    ];

    public function checkUserComments(User $user, Products $product): void
    {
        $countComments = 0;
        foreach ($user->getComments() as $comment) {
            if ($comment->getProduct()->getId() == $product->getId()) {
                $countComments++;
            }
        }
        if ($countComments >= self::LIMIT_COMMENTS_USER) {
            throw new ApiException(message: 'Превышен лимит комментариев к продукту');
        }
    }

}