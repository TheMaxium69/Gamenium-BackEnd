<?php

namespace App\Entity;

use App\Repository\CommentReplyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentReplyRepository::class)]
class CommentReply
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['commentreply:read', 'warn:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Groups(['commentreply:read'])]
    private ?Comment $comment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['commentreply:read'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['commentreply:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['commentreply:read'])]
    private ?\DateTimeInterface $last_edit = null;

    #[ORM\Column]
    #[Groups(['commentreply:read'])]
    private ?int $nb_edit = null;

    #[ORM\Column(length: 255)]
    #[Groups(['commentreply:read', 'warn:read'])]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

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

    public function getLastEdit(): ?\DateTimeInterface
    {
        return $this->last_edit;
    }

    public function setLastEdit(\DateTimeInterface $last_edit): static
    {
        $this->last_edit = $last_edit;

        return $this;
    }

    public function getNbEdit(): ?int
    {
        return $this->nb_edit;
    }

    public function setNbEdit(int $nb_edit): static
    {
        $this->nb_edit = $nb_edit;

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
}
