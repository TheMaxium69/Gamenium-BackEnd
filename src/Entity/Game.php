<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('game:read')]
    private int $id;

    #[ORM\OneToMany(targetEntity: GameActuality::class, mappedBy: 'game')]
    #[Groups('game:read')]
    private Collection $gameActualities;

    public function __construct()
    {
        $this->gameActualities = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, GameActuality>
     */
    public function getGameActualities(): Collection
    {
        return $this->gameActualities;
    }

    public function addGameActuality(GameActuality $gameActuality): static
    {
        if (!$this->gameActualities->contains($gameActuality)) {
            $this->gameActualities->add($gameActuality);
            $gameActuality->setGame($this);
        }

        return $this;
    }

    public function removeGameActuality(GameActuality $gameActuality): static
    {
        if ($this->gameActualities->removeElement($gameActuality)) {
            // set the owning side to null (unless already changed)
            if ($gameActuality->getGame() === $this) {
                $gameActuality->setGame(null);
            }
        }

        return $this;
    }
}
