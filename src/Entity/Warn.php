<?php

namespace App\Entity;

use App\Repository\WarnRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarnRepository::class)]
class Warn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WarnType $warnType = null;

    #[ORM\ManyToOne]
    private ?user $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $warnAt = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne]
    private ?User $profil = null;

    #[ORM\ManyToOne]
    private ?PostActu $actu = null;

    #[ORM\ManyToOne]
    private ?comment $comment = null;

    #[ORM\ManyToOne]
    private ?commentReply $commentReply = null;

    #[ORM\ManyToOne]
    private ?HistoryMyGame $hmg = null;

    #[ORM\ManyToOne]
    private ?historymyplateform $hmp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWarnType(): ?WarnType
    {
        return $this->warnType;
    }

    public function setWarnType(?WarnType $warnType): static
    {
        $this->warnType = $warnType;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getWarnAt(): ?\DateTimeImmutable
    {
        return $this->warnAt;
    }

    public function setWarnAt(\DateTimeImmutable $warnAt): static
    {
        $this->warnAt = $warnAt;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

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

    public function getProfil(): ?User
    {
        return $this->profil;
    }

    public function setProfil(?User $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

    public function getActu(): ?PostActu
    {
        return $this->actu;
    }

    public function setActu(?PostActu $actu): static
    {
        $this->actu = $actu;

        return $this;
    }

    public function getComment(): ?comment
    {
        return $this->comment;
    }

    public function setComment(?comment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCommentReply(): ?commentReply
    {
        return $this->commentReply;
    }

    public function setCommentReply(?commentReply $commentReply): static
    {
        $this->commentReply = $commentReply;

        return $this;
    }

    public function getHmg(): ?HistoryMyGame
    {
        return $this->hmg;
    }

    public function setHmg(?HistoryMyGame $hmg): static
    {
        $this->hmg = $hmg;

        return $this;
    }

    public function getHmp(): ?historymyplateform
    {
        return $this->hmp;
    }

    public function setHmp(?historymyplateform $hmp): static
    {
        $this->hmp = $hmp;

        return $this;
    }
}
