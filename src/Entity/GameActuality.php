<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\GameActualityRepository;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GameActualityRepository::class)]
class GameActuality
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('game:read')]
    private ?int $id;



    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups('game:read')]
    private ?DateTimeImmutable $joinedAt;

    #[ORM\OneToOne(targetEntity: Picture::class)]
    #[ORM\JoinColumn(name: 'id_picture_id', referencedColumnName: 'id', nullable: true)]
    #[Groups('game:read')]
    private ?Picture $picture;

    #[ORM\ManyToMany(targetEntity: Game::class, inversedBy: 'gameActualities')]
    #[Groups('game:read')]
    private Collection $game;


    public function __construct(Game $game)
    
{
    $this->game = new ArrayCollection();
    $this->joinedAt = new \DateTimeImmutable();
}
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getJoinedAt(): ?DateTimeImmutable
{
    return $this->joinedAt;
}


    public function setJoinedAt(?DateTimeImmutable $joinedAt): void
    {
        
        $this->joinedAt = $joinedAt;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGame(): Collection
    {
        return $this->game;
    }

    public function addGame(Game $game): static
    {
        if (!$this->game->contains($game)) {
            $this->game->add($game);
            $game->addGameActuality($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        $this->game->removeElement($game);
        $game->removeGameActuality($this);

        return $this;
    }
}
