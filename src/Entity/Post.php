<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_provider = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_game_actuality = null;

    #[ORM\Column]
    private ?int $id_user = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_edit = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_edit = null;

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

    public function getIdProvider(): ?int
    {
        return $this->id_provider;
    }

    public function setIdProvider(?int $id_provider): static
    {
        $this->id_provider = $id_provider;

        return $this;
    }

    public function getIdGameActuality(): ?int
    {
        return $this->id_game_actuality;
    }

    public function setIdGameActuality(?int $id_game_actuality): static
    {
        $this->id_game_actuality = $id_game_actuality;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

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

    public function getLastEdit(): ?\DateTimeInterface
    {
        return $this->last_edit;
    }

    public function setLastEdit(\DateTimeInterface $last_edit): static
    {
        $this->last_edit = $last_edit;

        return $this;
    }

    public function getNbEdit(): ?int
    {
        return $this->nb_edit;
    }

    public function setNbEdit(?int $nb_edit): static
    {
        $this->nb_edit = $nb_edit;

        return $this;
    }
}
