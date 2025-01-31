<?php

namespace App\Entity;

use App\Repository\LogRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LogRoleRepository::class)]
class LogRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['logrole:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['logrole:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logrole:read'])]
    private ?string $role = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logrole:read'])]
    private ?string $action = null;

    #[ORM\Column]
    #[Groups(['logrole:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['logrole:read'])]
    private ?user $action_by = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
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

    public function getActionBy(): ?user
    {
        return $this->action_by;
    }

    public function setActionBy(?user $action_by): static
    {
        $this->action_by = $action_by;

        return $this;
    }
}
