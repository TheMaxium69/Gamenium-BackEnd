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
    #[Groups('profile:read' , 'game:read')]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: GameProfile::class, mappedBy: 'game')]
    #[Groups('profile:read' , 'game:read')]
    private Collection $gameProfiles;

    public function __construct()
    {
        $this->gameProfiles = new ArrayCollection();
    }




    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, GameProfile>
     */
    public function getGameProfiles(): Collection
    {
        return $this->gameProfiles;
    }

    public function addGameProfile(GameProfile $gameProfile): static
    {
        if (!$this->gameProfiles->contains($gameProfile)) {
            $this->gameProfiles->add($gameProfile);
            $gameProfile->setGame($this);
        }

        return $this;
    }

    public function removeGameProfile(GameProfile $gameProfile): static
    {
        if ($this->gameProfiles->removeElement($gameProfile)) {
            // set the owning side to null (unless already changed)
            if ($gameProfile->getGame() === $this) {
                $gameProfile->setGame(null);
            }
        }

        return $this;
    }


}
