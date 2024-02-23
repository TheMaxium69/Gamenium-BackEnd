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
    #[Groups('badge:read' , 'profile:read')]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    #[Groups('badge:read', 'profile:read'  )]
    private ?string $url = null;

    #[ORM\Column]
    #[Groups('badge:read' , 'profile:read' )]
    private ?int $id_user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('badge:read')]
    private ?\DateTimeInterface $posted_at = null;

    #[ORM\Column(length: 255)]
    #[Groups('badge:read' , 'profile:read')]
    private ?string $ip = null;





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

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

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


}
