<?php

namespace App\Entity;

use App\Repository\DeviseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DeviseRepository::class)]
class Devise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['historygame:read', 'devise:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['historygame:read', 'devise:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['historygame:read', 'devise:read'])]
    private ?string $symbole = null;

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

    public function getSymbole(): ?string
    {
        return $this->symbole;
    }

    public function setSymbole(string $symbole): static
    {
        $this->symbole = $symbole;

        return $this;
    }
}
