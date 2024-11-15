<?php

namespace App\Entity;

use App\Repository\HmgScreenshotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HmgScreenshotRepository::class)]
class HmgScreenshot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?picture $picture = null;

    #[ORM\ManyToOne]
    private ?HmgCopy $Copy = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?HmgScreenshotCategory $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicture(): ?picture
    {
        return $this->picture;
    }

    public function setPicture(picture $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCopy(): ?HmgCopy
    {
        return $this->Copy;
    }

    public function setCopy(?HmgCopy $Copy): static
    {
        $this->Copy = $Copy;

        return $this;
    }

    public function getCategory(): ?HmgScreenshotCategory
    {
        return $this->category;
    }

    public function setCategory(?HmgScreenshotCategory $category): static
    {
        $this->category = $category;

        return $this;
    }
}
