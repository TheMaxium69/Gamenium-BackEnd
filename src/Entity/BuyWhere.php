<?php

namespace App\Entity;

use App\Repository\BuyWhereRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BuyWhereRepository::class)]
class BuyWhere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['buywhere:read','historygame:read','buywhereuser:read', 'historyplateform:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['buywhere:read','historygame:read','buywhereuser:read', 'historyplateform:read'])]
    private ?bool $is_public = null;


    #[ORM\Column(length: 255)]
    #[Groups(['buywhere:read','historygame:read','buywhereuser:read', 'historyplateform:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[Groups(['buywhereuser:read'])]
    private ?int $nb_use = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsPublic(): ?bool
    {
        return $this->is_public;
    }

    public function setIsPublic(bool $is_public): static
    {
        $this->is_public = $is_public;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getNbUse(): ?int
    {
        return $this->nb_use;
    }

    public function setNbUse(int $nb_use): static
    {
        $this->nb_use = $nb_use;

        return $this;
    }
}
