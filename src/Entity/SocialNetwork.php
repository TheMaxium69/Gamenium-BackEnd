<?php

namespace App\Entity;

use App\Repository\SocialNetworkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SocialNetworkRepository::class)]
class SocialNetwork
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['socialnetwork:read', 'profilSocialNetwork:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['socialnetwork:read', 'profilSocialNetwork:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url_api = null;

    #[ORM\Column(length: 255)]
    #[Groups(['profilSocialNetwork:read'])]
    private ?string $icon_class = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_connexion = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_profil = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUrlApi(): ?string
    {
        return $this->url_api;
    }

    public function setUrlApi(string $url_api): static
    {
        $this->url_api = $url_api;
        
        return $this;
    }

    public function getIconClass(): ?string
    {
        return $this->icon_class;
    }

    public function setIconClass(string $icon_class): static
    {
        $this->icon_class = $icon_class;

        return $this;
    }

    public function isIsConnexion(): ?bool
    {
        return $this->is_connexion;
    }

    public function setIsConnexion(?bool $is_connexion): static
    {
        $this->is_connexion = $is_connexion;

        return $this;
    }

    public function isIsProfil(): ?bool
    {
        return $this->is_profil;
    }

    public function setIsProfil(?bool $is_profil): static
    {
        $this->is_profil = $is_profil;

        return $this;
    }
}
