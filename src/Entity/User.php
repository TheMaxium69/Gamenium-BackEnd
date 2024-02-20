<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_useritium = null;

    #[ORM\Column]
    private array $user_role = [] ;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastConnection = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $ip = null;

    #[ORM\Column]
    private ?int $id_picture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUseritium(): ?int
    {
        return $this->id_useritium;
    }

    public function setIdUseritium(int $id_useritium): static
    {
        $this->id_useritium = $id_useritium;

        return $this;
    }

    public function getUserRole(): array
    {
        $user_role = $this->user_role;
        
        return array_unique($user_role);
    }

    public function setUserRole(array $user_role): static
    {
        $this->user_role = $user_role;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastConnection(): ?\DateTimeImmutable
    {
        return $this->lastConnection;
    }

    public function setLastConnection(\DateTimeImmutable $lastConnection): static
    {
        $this->lastConnection = $lastConnection;

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

    public function getIdPicture(): ?int
    {
        return $this->id_picture;
    }

    public function setIdPicture(int $id_picture): static
    {
        $this->id_picture = $id_picture;

        return $this;
    }
}
