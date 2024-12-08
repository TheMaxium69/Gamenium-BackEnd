<?php

namespace App\Entity;

use App\Repository\HmgSpeedrunRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HmgSpeedrunRepository::class)]
class HmgSpeedrun
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['historygame:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['historygame:read'])]
    private ?string $chrono = null;

    #[ORM\Column(length: 255)]
    #[Groups(['historygame:read'])]
    private ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['historygame:read'])]
    private ?string $link = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?HistoryMyGame $MyGame = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChrono(): ?string
    {
        return $this->chrono;
    }

    public function setChrono(string $chrono): static
    {
        $this->chrono = $chrono;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getMyGame(): ?HistoryMyGame
    {
        return $this->MyGame;
    }

    public function setMyGame(?HistoryMyGame $MyGame): static
    {
        $this->MyGame = $MyGame;

        return $this;
    }
}
