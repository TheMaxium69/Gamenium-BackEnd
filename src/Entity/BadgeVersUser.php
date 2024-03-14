<?php

namespace App\Entity;

use App\Repository\BadgeVersUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BadgeVersUserRepository::class)]
class BadgeVersUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['badgesversuser:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['badgesversuser:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['badgesversuser:read'])]
    private ?user $user = null;

    #[ORM\ManyToOne(targetEntity: Badge::class)]
    #[Groups(['badgesversuser:read'])]
    private ?Badge $badge = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

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

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBadge(?Badge $badge): static
    {
        $this->badge = $badge;

        return $this;
    }
}
