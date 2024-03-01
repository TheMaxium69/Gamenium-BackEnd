<?php

namespace App\Entity;

use App\Repository\HistoryMyGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HistoryMyGameRepository::class)]
class HistoryMyGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['historygame:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['historygame:read'])]
    private ?bool $is_favorite = null;

    #[ORM\Column(length: 255)]
    #[Groups(['historygame:read'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['historygame:read'])]
    private ?\DateTimeImmutable $buy_at = null;

    #[ORM\ManyToOne]
    #[Groups(['historygame:read'])]
    private ?user $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['historygame:read'])]
    private ?userrate $userrate = null;

    #[ORM\ManyToOne]
    #[Groups(['historygame:read'])]
    private ?buywhere $buywhere = null;


    public function __construct()
    {
        $this->user = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsFavorite(): ?bool
    {
        return $this->is_favorite;
    }

    public function setIsFavorite(bool $is_favorite): static
    {
        $this->is_favorite = $is_favorite;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getBuyAt(): ?\DateTimeImmutable
    {
        return $this->buy_at;
    }
public function setBuyAt(\DateTimeImmutable $buy_at): static
    {
        $this->buy_at = $buy_at;

        return $this;
    }

    public function getUserrate(): ?userrate
    {
        return $this->userrate;
    }

    public function setUserrate(?userrate $userrate): static
    {
        $this->userrate = $userrate;

        return $this;
    }

    public function getBuywhere(): ?buywhere
    {
        return $this->buywhere;
    }

    public function setBuywhere(?buywhere $buywhere): static
    {
        $this->buywhere = $buywhere;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }
}