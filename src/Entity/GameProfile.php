<?php

namespace App\Entity;

use App\Repository\GameProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Annotation\Exclude;

#[ORM\Entity(repositoryClass: GameProfileRepository::class)]
class GameProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['gameprofile:read' , 'post:read', 'follow:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups('gameprofile:read')]
    private ?\DateTimeImmutable $joined_at = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['gameprofile:read', 'follow:read'])]
    private ?picture $picture = null;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('gameprofile:read')]
    private ?game $game = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joined_at;
    }

    public function setJoinedAt(\DateTimeImmutable $joined_at): static
    {
        $this->joined_at = $joined_at;

        return $this;
    }

    public function getPicture(): ?picture
    {
        return $this->picture;
    }

    public function setPicture(?picture $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getGame(): ?game
    {
        return $this->game;
    }

    public function setGame(?game $game): static
    {
        $this->game = $game;

        return $this;
    }
}
