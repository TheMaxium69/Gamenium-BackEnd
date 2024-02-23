<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('post:read')]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: PostActu::class, mappedBy: 'Game')]
    private Collection $postActus;

    public function __construct()
    {
        $this->postActus = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, PostActu>
     */
    public function getPostActus(): Collection
    {
        return $this->postActus;
    }

    public function addPostActu(PostActu $postActu): static
    {
        if (!$this->postActus->contains($postActu)) {
            $this->postActus->add($postActu);
            $postActu->setGame($this);
        }

        return $this;
    }

    public function removePostActu(PostActu $postActu): static
    {
        if ($this->postActus->removeElement($postActu)) {
            // set the owning side to null (unless already changed)
            if ($postActu->getGame() === $this) {
                $postActu->setGame(null);
            }
        }

        return $this;
    }

}