<?php

namespace App\Entity;

use App\Repository\HmgCopyDlcRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HmgCopyDlcRepository::class)]
class HmgCopyDlc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?HmgCopyPurchase $purchase = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPurchase(): ?HmgCopyPurchase
    {
        return $this->purchase;
    }

    public function setPurchase(?HmgCopyPurchase $purchase): static
    {
        $this->purchase = $purchase;

        return $this;
    }
}
