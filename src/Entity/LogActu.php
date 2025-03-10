<?php

namespace App\Entity;

use App\Repository\LogActuRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LogActuRepository::class)]
class LogActu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['logactu:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['logactu:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['logactu:read'])]
    private ?PostActu $actu = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logactu:read'])]
    private ?string $action = null;

    #[ORM\Column]
    #[Groups(['logactu:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    #[Groups(['logactu:read'])]
    private ?string $route = null;

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

    public function getActu(): ?PostActu
    {
        return $this->actu;
    }

    public function setActu(?PostActu $actu): static
    {
        $this->actu = $actu;

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

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }
}
