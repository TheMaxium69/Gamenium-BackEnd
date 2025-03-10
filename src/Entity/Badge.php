<?php

namespace App\Entity;

use App\Repository\BadgeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: BadgeRepository::class)]
class Badge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['badge:read' , 'badgesversuser:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['badge:read','useradmin:read'])]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['badge:read','useradmin:read'])]
    private ?Picture $picture = null;
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('badge:read')]
    private ?string $unlockDescription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(Picture $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getUnlockDescription(): ?string
    {
        return $this->unlockDescription;
    }

    public function setUnlockDescription(?string $unlockDescription): static
    {
        $this->unlockDescription = $unlockDescription;

        return $this;
    }
}
