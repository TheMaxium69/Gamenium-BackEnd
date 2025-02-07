<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['historyplateform:read', 'logactu:read','usermodo:read', 'postactu:read', 'testRate:read', 'log:read','logrole:read', 'warn:read', 'useradmin:read', 'gameprofile:read', 'commentreply:read', 'screenshot:read' , 'picture:read' , 'provider:read' , 'badge:read','post:read', 'user:read','comment:read', 'follow:read', 'historygame:read', 'commentreply:admin', 'comment:admin'])]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    #[Groups(['historyplateform:read', 'logactu:read','usermodo:read', 'postactu:read', 'testRate:read', 'log:read','logrole:read', 'useradmin:read', 'warn:read', 'gameprofile:read', 'commentreply:read', 'screenshot:read' , 'picture:read' , 'provider:read' , 'badge:read','post:read', 'user:read','comment:read', 'follow:read', 'historygame:read', 'commentreply:admin', 'comment:admin'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $posted_at = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['picture:read', 'screenshot:read'])]
    private ?user $user = null;

    #[ORM\Column]
    private ?bool $is_deleted = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getPostedAt(): ?\DateTimeInterface
    {
        return $this->posted_at;
    }

    public function setPostedAt(\DateTimeInterface $posted_at): static
    {
        $this->posted_at = $posted_at;

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

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): static
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

}
