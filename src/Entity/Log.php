<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['usermodo:read', 'useradmin:read', 'log:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'logs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['log:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['useradmin:read', 'log:read'])]
    private ?string $why = null;

    #[ORM\Column]
    #[Groups(['useradmin:read', 'log:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['log:read'])]
    private ?user $moderated_by = null;

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

    public function getWhy(): ?string
    {
        return $this->why;
    }

    public function setWhy(string $why): static
    {
        $this->why = $why;

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

    public function getModeratedBy(): ?user
    {
        return $this->moderated_by;
    }

    public function setModeratedBy(?user $moderated_by): static
    {
        $this->moderated_by = $moderated_by;

        return $this;
    }
}
