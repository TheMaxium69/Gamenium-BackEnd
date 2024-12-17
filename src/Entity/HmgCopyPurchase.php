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

    #[ORM\ManyToOne]
    #[Groups(['historygame:read'])]
    private ?BuyWhere $buy_where = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['historygame:read'])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Devise')]
    #[Groups(['historygame:read'])]
    private ?devise $devise = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['historygame:read'])]
    private ?int $year_buy_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['historygame:read'])]
    private ?int $month_buy_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['historygame:read'])]
    private ?int $day_buy_at = null;

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

    public function getYearBuyAt(): ?int
    {
        return $this->year_buy_at;
    }

    public function setYearBuyAt(?int $year_buy_at): static
    {
        $this->year_buy_at = $year_buy_at;

        return $this;
    }

    public function getMonthBuyAt(): ?int
    {
        return $this->month_buy_at;
    }

    public function setMonthBuyAt(?int $month_buy_at): static
    {
        $this->month_buy_at = $month_buy_at;

        return $this;
    }

    public function getDayBuyAt(): ?int
    {
        return $this->day_buy_at;
    }

    public function setDayBuyAt(?int $day_buy_at): static
    {
        $this->day_buy_at = $day_buy_at;

        return $this;
    }
}
