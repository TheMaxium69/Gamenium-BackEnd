<?php

namespace App\Entity;

use App\Repository\HistoryMyPlateformRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryMyPlateformRepository::class)]
class HistoryMyPlateform
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?plateform $plateform = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $added_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlateform(): ?plateform
    {
        return $this->plateform;
    }

    public function setPlateform(?plateform $plateform): static
    {
        $this->plateform = $plateform;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->added_at;
    }

    public function setAddedAt(\DateTimeImmutable $added_at): static
    {
        $this->added_at = $added_at;

        return $this;
    }
}
