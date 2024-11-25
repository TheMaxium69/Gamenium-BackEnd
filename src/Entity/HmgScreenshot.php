<?php

namespace App\Entity;

use App\Repository\HmgScreenshotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HmgScreenshotRepository::class)]
class HmgScreenshot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['historygame:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: 'App\Entity\picture', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['historygame:read'])]
    private ?picture $picture = null;

    #[ORM\ManyToOne]
    #[Groups(['historygame:read'])]
    private ?HmgCopy $Copy = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['historygame:read'])]
    private ?HmgScreenshotCategory $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?HistoryMyGame $MyGame = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicture(): ?picture
    {
        return $this->picture;
    }

    public function setPicture(picture $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCopy(): ?HmgCopy
    {
        return $this->Copy;
    }

    public function setCopy(?HmgCopy $Copy): static
    {
        $this->Copy = $Copy;

        return $this;
    }

    public function getCategory(): ?HmgScreenshotCategory
    {
        return $this->category;
    }

    public function setCategory(?HmgScreenshotCategory $category): static
    {
        $this->category = $category;

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
