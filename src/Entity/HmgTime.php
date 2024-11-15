<?php

namespace App\Entity;

use App\Repository\HmgTimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HmgTimeRepository::class)]
class HmgTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }
}
