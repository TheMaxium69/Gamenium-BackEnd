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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['historygame:read'])]
    private ?\DateTimeInterface $added_at = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['historygame:read'])]
    private ?user $user = null;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['historygame:read'])]
    private ?game $game = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['historygame:read'])]
    private ?int $difficulty_rating = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['historygame:read'])]
    private ?int $lifetime_rating = null;

    #[ORM\Column]
    #[Groups(['historygame:read'])]
    private ?bool $wish_list = null;

    #[ORM\ManyToOne(targetEntity: Plateform::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['historygame:read'])]
    private ?plateform $plateform = null;

    #[ORM\ManyToMany(targetEntity: HmgTags::class, mappedBy: 'HistoryMyGame')]
    private Collection $hmgTags;

    public function __construct()
    {
        $this->hmgTags = new ArrayCollection();
    }


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

    public function getDifficultyRating(): ?int
    {
        return $this->difficulty_rating;
    }

    public function setDifficultyRating(?int $difficulty_rating): static
    {
        $this->difficulty_rating = $difficulty_rating;

        return $this;
    }

    public function getLifetimeRating(): ?int
    {
        return $this->lifetime_rating;
    }

    public function setLifetimeRating(?int $lifetime_rating): static
    {
        $this->lifetime_rating = $lifetime_rating;

        return $this;
    }

    public function isWishList(): ?bool
    {
        return $this->wish_list;
    }

    public function setWishList(bool $wish_list): static
    {
        $this->wish_list = $wish_list;

        return $this;
    }

    public function getPlateform(): ?plateform
    {
        return $this->plateform;
    }

    public function setPlateform(?plateform $plateform): static
    {
        $this->plateform = $plateform;

        return $this;
    }

    /**
     * @return Collection<int, HmgTags>
     */
    public function getHmgTags(): Collection
    {
        return $this->hmgTags;
    }

    public function addHmgTag(HmgTags $hmgTag): static
    {
        if (!$this->hmgTags->contains($hmgTag)) {
            $this->hmgTags->add($hmgTag);
            $hmgTag->addHistoryMyGame($this);
        }

        return $this;
    }

    public function removeHmgTag(HmgTags $hmgTag): static
    {
        if ($this->hmgTags->removeElement($hmgTag)) {
            $hmgTag->removeHistoryMyGame($this);
        }

        return $this;
    }

}