<?php

namespace App\Entity;

use App\Repository\HmpCopyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HmpCopyRepository::class)]
class HmpCopy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?HistoryMyPlateform $history_my_plateform = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $edition = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $barcode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isBox = null;

    #[ORM\ManyToOne]
    private ?hmgCopyEtat $etat = null;

    #[ORM\ManyToOne]
    private ?hmgCopyPurchase $purchase = null;

    #[ORM\ManyToOne]
    private ?hmgCopyRegion $region = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHistoryMyPlateform(): ?HistoryMyPlateform
    {
        return $this->history_my_plateform;
    }

    public function setHistoryMyPlateform(?HistoryMyPlateform $history_my_plateform): static
    {
        $this->history_my_plateform = $history_my_plateform;

        return $this;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function setEdition(?string $edition): static
    {
        $this->edition = $edition;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): static
    {
        $this->barcode = $barcode;

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

    public function isIsBox(): ?bool
    {
        return $this->isBox;
    }

    public function setIsBox(?bool $isBox): static
    {
        $this->isBox = $isBox;

        return $this;
    }

    public function getEtat(): ?hmgCopyEtat
    {
        return $this->etat;
    }

    public function setEtat(?hmgCopyEtat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getPurchase(): ?hmgCopyPurchase
    {
        return $this->purchase;
    }

    public function setPurchase(?hmgCopyPurchase $purchase): static
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getRegion(): ?hmgCopyRegion
    {
        return $this->region;
    }

    public function setRegion(?hmgCopyRegion $region): static
    {
        $this->region = $region;

        return $this;
    }
}
