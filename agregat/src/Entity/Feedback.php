<?php

namespace App\Entity;

use App\Helper\EnumStatus\FeedbackStatus;
use App\Repository\FeedbackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_feedback'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get_feedback'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get_feedback'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_feedback'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['get_feedback'])]
    private ?string $message = null;

    #[ORM\Column]
    private ?int $status;

    public function __construct()
    {
        $this->status = FeedbackStatus::ACTIVE->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
