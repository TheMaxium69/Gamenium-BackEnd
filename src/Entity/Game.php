<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['game:read','gameprofile:read', 'post:read' , 'userRate:read'])]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: PostActu::class, mappedBy: 'Game')]
    private Collection $postActus;

    #[ORM\Column]
    #[Groups(['game:read'])]
    private ?int $id_GiantBomb = null;

    #[ORM\Column(length: 255)]
    #[Groups(['game:read'])]
    private ?string $guid = null;

    #[ORM\Column(length: 255)]
    #[Groups(['game:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['game:read'])]
    private ?string $aliases = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['game:read'])]
    private ?string $apiDetailUrl = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read'])]
    private ?\DateTimeImmutable $dateAdded = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['game:read'])]
    private ?\DateTimeInterface $dateLastUpdated = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['game:read'])]
    private ?string $deck = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['game:read'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read'])]
    private ?int $expectedReleaseDay = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['game:read'])]
    private ?string $expectedReleaseMonth = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read'])]
    private ?int $expectedReleaseYear = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read'])]
    private ?array $image = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read'])]
    private ?array $imageTags = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read'])]
    private ?int $numberOfUserReviews = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read'])]
    private ?array $originalGameRating = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalReleaseDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read'])]
    private ?array $platforms = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['game:read'])]
    private ?string $siteDetailUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $expected_release_quarter = null;

    public function __construct()
    {
        $this->postActus = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGiantBomb(): ?int
    {
        return $this->id_GiantBomb;
    }

    public function setIdGiantBomb(int $id_GiantBomb): static
    {
        $this->id_GiantBomb = $id_GiantBomb;

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): static
    {
        $this->guid = $guid;

        return $this;
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

    public function getAliases(): ?string
    {
        return $this->aliases;
    }

    public function setAliases(?string $aliases): static
    {
        $this->aliases = $aliases;

        return $this;
    }

    public function getApiDetailUrl(): ?string
    {
        return $this->apiDetailUrl;
    }

    public function setApiDetailUrl(?string $apiDetailUrl): static
    {
        $this->apiDetailUrl = $apiDetailUrl;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeImmutable
    {
        return $this->dateAdded;
    }

    public function setDateAdded(?\DateTimeImmutable $dateAdded): static
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    public function getDateLastUpdated(): ?\DateTimeInterface
    {
        return $this->dateLastUpdated;
    }

    public function setDateLastUpdated(?\DateTimeInterface $dateLastUpdated): static
    {
        $this->dateLastUpdated = $dateLastUpdated;

        return $this;
    }

    public function getDeck(): ?string
    {
        return $this->deck;
    }

    public function setDeck(?string $deck): static
    {
        $this->deck = $deck;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getExpectedReleaseDay(): ?int
    {
        return $this->expectedReleaseDay;
    }

    public function setExpectedReleaseDay(?int $expectedReleaseDay): static
    {
        $this->expectedReleaseDay = $expectedReleaseDay;

        return $this;
    }

    public function getExpectedReleaseMonth(): ?string
    {
        return $this->expectedReleaseMonth;
    }

    public function setExpectedReleaseMonth(?string $expectedReleaseMonth): static
    {
        $this->expectedReleaseMonth = $expectedReleaseMonth;

        return $this;
    }

    public function getExpectedReleaseYear(): ?int
    {
        return $this->expectedReleaseYear;
    }

    public function setExpectedReleaseYear(?int $expectedReleaseYear): static
    {
        $this->expectedReleaseYear = $expectedReleaseYear;

        return $this;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(?array $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImageTags(): ?array
    {
        return $this->imageTags;
    }

    public function setImageTags(?array $imageTags): static
    {
        $this->imageTags = $imageTags;

        return $this;
    }

    public function getNumberOfUserReviews(): ?int
    {
        return $this->numberOfUserReviews;
    }

    public function setNumberOfUserReviews(?int $numberOfUserReviews): static
    {
        $this->numberOfUserReviews = $numberOfUserReviews;

        return $this;
    }

    public function getOriginalGameRating(): ?array
    {
        return $this->originalGameRating;
    }

    public function setOriginalGameRating(?array $originalGameRating): static
    {
        $this->originalGameRating = $originalGameRating;

        return $this;
    }

    public function getOriginalReleaseDate(): ?string
    {
        return $this->originalReleaseDate;
    }

    public function setOriginalReleaseDate(?string $originalReleaseDate): static
    {
        $this->originalReleaseDate = $originalReleaseDate;

        return $this;
    }

    public function getPlatforms(): ?array
    {
        return $this->platforms;
    }

    public function setPlatforms(?array $platforms): static
    {
        $this->platforms = $platforms;

        return $this;
    }

    public function getSiteDetailUrl(): ?string
    {
        return $this->siteDetailUrl;
    }

    public function setSiteDetailUrl(?string $siteDetailUrl): static
    {
        $this->siteDetailUrl = $siteDetailUrl;

        return $this;
    }

    public function getExpectedReleaseQuarter(): ?string
    {
        return $this->expected_release_quarter;
    }

    public function setExpectedReleaseQuarter(?string $expected_release_quarter): static
    {
        $this->expected_release_quarter = $expected_release_quarter;

        return $this;
    }

}