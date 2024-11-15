<?php

namespace App\Entity;

use App\Repository\HmgSpeedrunRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HmgSpeedrunRepository::class)]
class HmgSpeedrun
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $chrono = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChrono(): ?\DateTimeInterface
    {
        return $this->chrono;
    }

    public function setChrono(\DateTimeInterface $chrono): static
    {
        $this->chrono = $chrono;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }
}
