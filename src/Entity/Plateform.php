<?php

namespace App\Entity;

use App\Repository\PlateformRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlateformRepository::class)]
class Plateform
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['plateform:read', 'historygame:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['plateform:read', 'historygame:read'])]
    private ?int $id_giant_bomb = null;

    #[ORM\Column(length: 255)]
    #[Groups(['plateform:read'])]
    private ?string $guid = null;

    #[ORM\Column(length: 255)]
    #[Groups(['plateform:read', 'historygame:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $aliases = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $api_detail_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $abbreviation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['plateform:read'])]
    private ?array $company = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['plateform:read'])]
    private ?\DateTimeImmutable $date_added = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['plateform:read'])]
    private ?\DateTimeImmutable $date_last_updated = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $deck = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['plateform:read'])]
    private ?array $image = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['plateform:read'])]
    private ?array $image_tags = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $install_base = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $online_support = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $original_price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['plateform:read'])]
    private ?\DateTimeImmutable $release_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plateform:read'])]
    private ?string $site_detail_url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGiantBomb(): ?int
    {
        return $this->id_giant_bomb;
    }

    public function setIdGiantBomb(int $id_giant_bomb): static
    {
        $this->id_giant_bomb = $id_giant_bomb;

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
        return $this->api_detail_url;
    }

    public function setApiDetailUrl(?string $api_detail_url): static
    {
        $this->api_detail_url = $api_detail_url;

        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): static
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    public function getCompany(): ?array
    {
        return $this->company;
    }

    public function setCompany(?array $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeImmutable
    {
        return $this->date_added;
    }

    public function setDateAdded(?\DateTimeImmutable $date_added): static
    {
        $this->date_added = $date_added;

        return $this;
    }

    public function getDateLastUpdated(): ?\DateTimeImmutable
    {
        return $this->date_last_updated;
    }

    public function setDateLastUpdated(?\DateTimeImmutable $date_last_updated): static
    {
        $this->date_last_updated = $date_last_updated;

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
        return $this->image_tags;
    }

    public function setImageTags(?array $image_tags): static
    {
        $this->image_tags = $image_tags;

        return $this;
    }

    public function getInstallBase(): ?string
    {
        return $this->install_base;
    }

    public function setInstallBase(?string $install_base): static
    {
        $this->install_base = $install_base;

        return $this;
    }

    public function getOnlineSupport(): ?string
    {
        return $this->online_support;
    }

    public function setOnlineSupport(?string $online_support): static
    {
        $this->online_support = $online_support;

        return $this;
    }

    public function getOriginalPrice(): ?string
    {
        return $this->original_price;
    }

    public function setOriginalPrice(?string $original_price): static
    {
        $this->original_price = $original_price;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->release_date;
    }

    public function setReleaseDate(?\DateTimeImmutable $release_date): static
    {
        $this->release_date = $release_date;

        return $this;
    }

    public function getSiteDetailUrl(): ?string
    {
        return $this->site_detail_url;
    }

    public function setSiteDetailUrl(?string $site_detail_url): static
    {
        $this->site_detail_url = $site_detail_url;

        return $this;
    }
}
