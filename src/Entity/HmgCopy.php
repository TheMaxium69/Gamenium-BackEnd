<?php

namespace App\Entity;

use App\Repository\HmgCopyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HmgCopyRepository::class)]
class HmgCopy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $edition = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $barcode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'hmgCopies')]
    private ?HmgCopyEtat $etat = null;

    #[ORM\ManyToOne(inversedBy: 'hmgCopies')]
    private ?HmgCopyFormat $format = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?hmgCopyPurchase $purchase = null;

    #[ORM\ManyToOne]
    private ?HmgCopyRegion $region = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?HistoryMyGame $HistoryMyGame = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEtat(): ?HmgCopyEtat
    {
        return $this->etat;
    }

    public function setEtat(?HmgCopyEtat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getFormat(): ?HmgCopyFormat
    {
        return $this->format;
    }

    public function setFormat(?HmgCopyFormat $format): static
    {
        $this->format = $format;

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

    public function getRegion(): ?HmgCopyRegion
    {
        return $this->region;
    }

    public function setRegion(?HmgCopyRegion $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getHistoryMyGame(): ?HistoryMyGame
    {
        return $this->HistoryMyGame;
    }

    public function setHistoryMyGame(?HistoryMyGame $HistoryMyGame): static
    {
        $this->HistoryMyGame = $HistoryMyGame;

        return $this;
    }

}
