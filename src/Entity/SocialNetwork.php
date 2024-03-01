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
    #[Groups(['socialnetwork:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['socialnetwork:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $url_api = null;

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
}
