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
    private ?int $id = null;




    public function getId(): int
    {
        return $this->id;
    }


}
