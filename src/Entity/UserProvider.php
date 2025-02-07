<?php

namespace App\Entity;

use App\Repository\UserProviderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserProviderRepository::class)]
class UserProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['userprovider:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne (targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['userprovider:read'])]
    private ?user $user = null;

    #[ORM\ManyToOne (targetEntity: Provider::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['userprovider:read'])]
    private ?provider $provider = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProvider(): ?provider
    {
        return $this->provider;
    }

    public function setProvider(?provider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }
}
