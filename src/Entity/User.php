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
    #[Groups('user:read')]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_useritium = null;

    #[ORM\Column]
    private array $user_role = [] ;

    #[ORM\Column]
    private ?\DateTimeImmutable $joinAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastConnection = null;

    #[ORM\Column]
    private ?int $id_picture = null;

    #[ORM\Column]
    private array $ip = [];

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $displayname_useritium = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $displayname = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $token = null;

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

    public function getJoinAt(): ?\DateTimeImmutable
    {
        return $this->joinAt;
    }

    public function setJoinAt(\DateTimeImmutable $joinAt): static
    {
        $this->joinAt = $joinAt;

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

    public function getIdPicture(): ?int
    {
        return $this->id_picture;
    }

    public function setIdPicture(int $id_picture): static
    {
        $this->id_picture = $id_picture;

        return $this;
    }

    public function getIp(): array
    {
        return $this->ip;
    }

    public function setIp(array $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getDisplaynameUseritium(): ?string
    {
        return $this->displayname_useritium;
    }

    public function setDisplaynameUseritium(?string $displayname_useritium): static
    {
        $this->displayname_useritium = $displayname_useritium;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getDisplayname(): ?string
    {
        return $this->displayname;
    }

    public function setDisplayname(string $displayname): static
    {
        $this->displayname = $displayname;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
