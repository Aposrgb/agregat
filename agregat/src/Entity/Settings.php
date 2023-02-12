<?php

namespace App\Entity;

use App\Helper\EnumType\SettingsType;
use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $senderTo = null;

    #[ORM\Column]
    private ?int $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderTo(): ?string
    {
        return $this->senderTo;
    }

    public function setSenderTo(?string $senderTo): self
    {
        $this->senderTo = $senderTo;

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

    public function getTypeName(): string
    {
        return SettingsType::tryFrom($this->type)->getName();
    }
}
