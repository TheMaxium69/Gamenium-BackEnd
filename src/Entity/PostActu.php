<?php

namespace App\Entity;

use App\Repository\PostActuRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostActuRepository::class)]
class PostActu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups('post:read')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    #[Groups('post:read')]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('post:read')]
    private ?\DateTimeInterface $last_edit = null;

    #[ORM\Column(nullable: true)]
    #[Groups('post:read')]
    private ?int $nb_edit = null;

    #[ORM\ManyToOne(inversedBy: 'postActus')]
    #[Groups('post:read')]
    private ?Provider $Provider = null;

    #[ORM\ManyToOne(inversedBy: 'postActus')]
    #[Groups('post:read')]
    private ?GameProfile $GameProfile = null;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getLastEdit(): ?\DateTimeInterface
    {
        return $this->last_edit;
    }

    public function setLastEdit(?\DateTimeInterface $last_edit): static
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

    public function getProvider(): ?Provider
    {
        return $this->Provider;
    }

    public function setProvider(?Provider $Provider): static
    {
        $this->Provider = $Provider;

        return $this;
    }

    public function getGameProfile(): ?GameProfile
    {
        return $this->GameProfile;
    }

    public function setGameProfile(?GameProfile $GameProfile): static
    {
        $this->GameProfile = $GameProfile;

        return $this;
    }
}