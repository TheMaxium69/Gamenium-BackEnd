<?php

namespace App\Entity;

use App\Repository\GameActualityRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameActualityRepository::class)]
class GameActuality
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idPicture = null;
    #[ORM\Column]
    private ?\DateTimeImmutable $JoinedAt = null;

    #[ORM\ManyToOne(inversedBy: 'gameActualities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPictureId(): ?int
    {
        return $this->idPicture;
    }

    public function setPictureId($pictureId): static
    {
        $this->idPicture = $pictureId;
        return $this;
    }

    public function getJoinedAt(): ?DateTimeImmutable
    {
        return $this->JoinedAt;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }
    
}
