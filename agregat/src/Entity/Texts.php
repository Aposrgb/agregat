<?php

namespace App\Entity;

use App\Helper\EnumType\TextsSubType;
use App\Repository\TextsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TextsRepository::class)]
class Texts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_texts'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['get_texts', 'get_texts_contact'])]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column]
    #[Groups(['get_texts', 'get_texts_contact'])]
    private ?int $subType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSubType(): ?int
    {
        return $this->subType;
    }

    public function setSubType(int $subType): self
    {
        $this->subType = $subType;

        return $this;
    }

    public function getSubTypeName(): string
    {
        return TextsSubType::tryFrom($this->subType)->getTypeName();
    }
}
