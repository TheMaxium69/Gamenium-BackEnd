<?php

namespace App\Entity;

use App\Repository\ProfilSocialNetworkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfilSocialNetworkRepository::class)]
class ProfilSocialNetwork
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['profilSocialNetwork:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['profilSocialNetwork:read'])]
    private ?string $url = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user = null;

    #[ORM\ManyToOne(targetEntity: SocialNetwork::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['profilSocialNetwork:read'])]
    private ?SocialNetwork $socialnetwork = null;

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

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSocialnetwork(): ?SocialNetwork
    {
        return $this->socialnetwork;
    }

    public function setSocialnetwork(?SocialNetwork $socialnetwork): static
    {
        $this->socialnetwork = $socialnetwork;

        return $this;
    }
}
