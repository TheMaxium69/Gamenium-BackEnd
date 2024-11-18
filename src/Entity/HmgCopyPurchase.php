<?php

namespace App\Entity;

use App\Repository\HmgCopyPurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HmgCopyPurchaseRepository::class)]
class HmgCopyPurchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['historygame:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['historygame:read'])]
    private ?int $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['historygame:read'])]
    private ?\DateTimeInterface $buy_date = null;

    #[ORM\ManyToOne]
    #[Groups(['historygame:read'])]
    private ?BuyWhere $buy_where = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['historygame:read'])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Devise')]
    #[Groups(['historygame:read'])]
    private ?devise $devise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getBuyDate(): ?\DateTimeInterface
    {
        return $this->buy_date;
    }

    public function setBuyDate(?\DateTimeInterface $buy_date): static
    {
        $this->buy_date = $buy_date;

        return $this;
    }

    public function getBuyWhere(): ?BuyWhere
    {
        return $this->buy_where;
    }

    public function setBuyWhere(?BuyWhere $buy_where): static
    {
        $this->buy_where = $buy_where;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDevise(): ?devise
    {
        return $this->devise;
    }

    public function setDevise(?devise $devise): static
    {
        $this->devise = $devise;

        return $this;
    }
}
