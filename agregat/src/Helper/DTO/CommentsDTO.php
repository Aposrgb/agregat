<?php

namespace App\Helper\DTO;

use App\Service\ValidatorService;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CommentsDTO
{
    #[Property(type: 'string')]
    #[Assert\NotBlank(groups: ['create_comments'])]
    #[Assert\Type(type: 'string', groups: ['create_comments'])]
    #[Groups(groups: ['create_comments'])]
    private $text = null;

    #[Property(type: 'integer')]
    #[Assert\NotBlank(groups: ['create_comments'])]
    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['create_comments'])]
    #[Assert\Range(min: 1, max: 5, groups: ['create_comments'])]
    #[Groups(groups: ['create_comments'])]
    private $rating = null;

    #[Property(type: 'file')]
    #[Groups(groups: ['create_comments'])]
    private $img1 = null;

    #[Property(type: 'file')]
    #[Groups(groups: ['create_comments'])]
    private $img2 = null;

    #[Property(type: 'file')]
    #[Groups(groups: ['create_comments'])]
    private $img3 = null;

    #[Property(type: 'file')]
    #[Groups(groups: ['create_comments'])]
    private $img4 = null;

    #[Property(type: 'file')]
    #[Groups(groups: ['create_comments'])]
    private $img5 = null;

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    public function getImg1()
    {
        return $this->img1;
    }

    public function setImg1($img1)
    {
        $this->img1 = $img1;
        return $this;
    }

    public function getImg2()
    {
        return $this->img2;
    }

    public function setImg2($img2)
    {
        $this->img2 = $img2;
        return $this;
    }

    public function getImg3()
    {
        return $this->img3;
    }

    public function setImg3($img3)
    {
        $this->img3 = $img3;
        return $this;
    }

    public function getImg4()
    {
        return $this->img4;
    }

    public function setImg4($img4)
    {
        $this->img4 = $img4;
        return $this;
    }

    public function getImg5()
    {
        return $this->img5;
    }

    public function setImg5($img5)
    {
        $this->img5 = $img5;
        return $this;
    }

}