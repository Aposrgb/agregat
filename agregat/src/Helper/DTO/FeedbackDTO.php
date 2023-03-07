<?php

namespace App\Helper\DTO;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class FeedbackDTO
{
    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['create_feedback'])]
    #[Assert\Type(type: 'string', groups: ['create_feedback'])]
    #[Groups(['create_feedback'])]
    private $name = null;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['create_feedback'])]
    #[Assert\Type(type: 'string', groups: ['create_feedback'])]
    #[Groups(['create_feedback'])]
    private $phone = null;

    /** @OA\Property(type="string") */
    #[Assert\Type(type: 'string', groups: ['create_feedback'])]
    #[Groups(['create_feedback'])]
    private $email = null;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(groups: ['create_feedback'])]
    #[Assert\Type(type: 'string', groups: ['create_feedback'])]
    #[Groups(['create_feedback'])]
    private $message = null;

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     * @return FeedbackDTO
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param null $phone
     * @return FeedbackDTO
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param null $email
     * @return FeedbackDTO
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $message
     * @return FeedbackDTO
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

}