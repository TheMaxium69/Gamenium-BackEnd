<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['provider:read','post:read','followProvider:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider:read','post:read'])]
    private ?string $tagName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider:read','post:read'])]
    private ?string $displayName = null;

    #[ORM\Column]
    #[Groups(['provider:read'])]
    private ?int $country = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['provider:read'])]
    private ?\DateTimeImmutable $joindeAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

//    #[ORM\Column(nullable: true)]
//    #[Groups(['provider:read','post:read'])]
//    private ?int $parentCompany = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['provider:read'])]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['provider:read','post:read'])]
    private ?int $banner = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['provider:read','post:read'])]
    private ?Picture $picture = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider:read','post:read'])]
    private ?string $color = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[Groups(['provider:read','post:read'])]
    private ?self $parentCompany = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTagName(): ?string
    {
        return $this->tagName;
    }

    public function setTagName(string $tagName): static
    {
        $this->tagName = $tagName;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getCountry(): ?int
    {
        return $this->country;
    }

    public function setCountry(int $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getJoindeAt(): ?\DateTimeImmutable
    {
        return $this->joindeAt;
    }

    public function setJoindeAt(\DateTimeImmutable $joindeAt): static
    {
        $this->joindeAt = $joindeAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

//    public function getParentCompany(): ?int
//    {
//        return $this->parentCompany;
//    }
//
//    public function setParentCompany(int $parentCompany): static
//    {
//        $this->parentCompany = $parentCompany;
//
//        return $this;
//    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getBanner(): ?int
    {
        return $this->banner;
    }

    public function setBanner(int $banner): static
    {
        $this->banner = $banner;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getParentCompany(): ?self
    {
        return $this->parentCompany;
    }

    public function setParentCompany(?self $parentCompany): static
    {
        $this->parentCompany = $parentCompany;

        return $this;
    }
}
