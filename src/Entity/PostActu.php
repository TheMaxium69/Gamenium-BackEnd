<?php

namespace App\Entity;

use App\Repository\PostActuRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostActuRepository::class)]
class PostActu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post:read' ,'comment:read' , 'like:read','followPageGame:read', 'view:read', 'warn:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['post:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post:read'])]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['post:read'])]
    private ?\DateTimeInterface $last_edit = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_edit = null;


    #[ORM\ManyToOne(inversedBy: 'postActus', targetEntity: Provider::class)]
    #[Groups(['post:read'])]
    private ?Provider $Provider = null;

    #[ORM\ManyToOne(inversedBy: 'postActus', targetEntity: GameProfile::class)]
    #[Groups(['post:read'])]
    private ?GameProfile $GameProfile = null;

    #[ORM\ManyToOne(inversedBy: 'postActus', targetEntity: Game::class)]
    #[Groups(['post:read'])]
    private ?Game $Game = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:read'])]
    private ?user $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post:read', 'view:read'])]
    private ?string $title = null;

    #[ORM\ManyToOne(targetEntity: Picture::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:read'])]
    private ?Picture $picture = null;

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

    public function getGame(): ?Game
    {
        return $this->Game;
    }

    public function setGame(?Game $Game): static
    {
        $this->Game = $Game;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): static
    {
        $this->picture = $picture;

        return $this;
    }
}