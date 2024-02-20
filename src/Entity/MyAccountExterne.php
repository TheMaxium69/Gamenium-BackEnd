<?php

namespace App\Entity;

use App\Repository\MyAccountExterneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MyAccountExterneRepository::class)]
class MyAccountExterne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_network = null;

    #[ORM\Column]
    private ?int $id_user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $api_key = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdNetwork(): ?int
    {
        return $this->id_network;
    }

    public function setIdNetwork(int $id_network): static
    {
        $this->id_network = $id_network;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->api_key;
    }

    public function setApiKey(string $api_key): static
    {
        $this->api_key = $api_key;

        return $this;
    }
}
