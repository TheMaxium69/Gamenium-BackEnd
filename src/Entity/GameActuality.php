<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\GameActualityRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: GameActualityRepository::class)]
class GameActuality
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('game:read')]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups('game:read')]
    private ?int $idPicture = null;
    
    #[ORM\Column]
    #[Groups('game:read')]
    private ?\DateTimeImmutable $JoinedAt = null;


    
    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'game_actuality')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('game:read')]
    private ?Game $game = null;

    public function __construct(Game $game)
    {
        $this->JoinedAt = new DateTimeImmutable();

    }

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
