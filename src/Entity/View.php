<?php

namespace App\Entity;

use App\Repository\ViewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ViewRepository::class)]
class View
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['view:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Groups(['view:read'])]
    private ?PostActu $PostActu = null;

    #[ORM\ManyToOne]
    #[Groups(['view:read'])]
    private ?Provider $Provider = null;

    #[ORM\ManyToOne]
    #[Groups(['view:read'])]
    private ?User $profile = null;

    #[ORM\ManyToOne]
    #[Groups(['view:read'])]
    private ?Game $Game = null;

    #[ORM\ManyToOne]
    private ?user $who = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $view_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostActu(): ?PostActu
    {
        return $this->PostActu;
    }

    public function setPostActu(?PostActu $PostActu): static
    {
        $this->PostActu = $PostActu;

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

    public function getProfile(): ?User
    {
        return $this->profile;
    }

    public function setProfile(?User $profile): static
    {
        $this->profile = $profile;

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

    public function getWho(): ?user
    {
        return $this->who;
    }

    public function setWho(?user $who): static
    {
        $this->who = $who;

        return $this;
    }

    public function getViewAt(): ?\DateTimeImmutable
    {
        return $this->view_at;
    }

    public function setViewAt(\DateTimeImmutable $view_at): static
    {
        $this->view_at = $view_at;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }
}
