<?php

namespace App\Entity;

use App\Repository\HistoryMyGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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
    private ?bool $is_pinned = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['historygame:read'])]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['historygame:read'])]
    private ?\DateTimeImmutable $buy_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['historygame:read'])]
    private ?\DateTimeInterface $added_at = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['historygame:read'])]
    private ?user $user = null;


    #[ORM\ManyToOne]
    #[Groups(['historygame:read'])]
    private ?buywhere $buywhere = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['historygame:read'])]
    private ?game $game = null;


//    public function __construct()
//    {
//        $this->user = new ArrayCollection();
//    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsPinned(): ?bool
    {
        return $this->is_pinned;
    }

    public function setIsPinned(bool $is_pinned): static
    {
        $this->is_pinned = $is_pinned;

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

    public function getGame(): ?game
    {
        return $this->game;
    }

    public function setGame(?game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeInterface
    {
        return $this->added_at;
    }

    public function setAddedAt(\DateTimeInterface $added_at): static
    {
        $this->added_at = $added_at;

        return $this;
    }

}