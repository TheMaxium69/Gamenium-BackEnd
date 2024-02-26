<?php

namespace App\Entity;

use App\Repository\HistoryMyGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryMyGameRepository::class)]
class HistoryMyGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column]
    private ?bool $is_favorite = null;

    #[ORM\Column]
    private ?int $id_note_user = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $buy_at = null;

    #[ORM\Column]
    private ?int $id_buy_where = null;

    #[ORM\ManyToOne]
    #[Groups('user:read')]
    private ?user $user = null;




    public function __construct()
    {
        $this->user = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }


    public function isIsFavorite(): ?bool
    {
        return $this->is_favorite;
    }

    public function setIsFavorite(bool $is_favorite): static
    {
        $this->is_favorite = $is_favorite;

        return $this;
    }

    public function getIdNoteUser(): ?int
    {
        return $this->id_note_user;
    }

    public function setIdNoteUser(int $id_note_user): static
    {
        $this->id_note_user = $id_note_user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getBuyAt(): ?\DateTimeImmutable
    {
        return $this->buy_at;
    }

    public function setBuyAt(\DateTimeImmutable $buy_at): static
    {
        $this->buy_at = $buy_at;

        return $this;
    }

    public function getIdBuyWhere(): ?int
    {
        return $this->id_buy_where;
    }

    public function setIdBuyWhere(int $id_buy_where): static
    {
        $this->id_buy_where = $id_buy_where;

        return $this;
    }

}
