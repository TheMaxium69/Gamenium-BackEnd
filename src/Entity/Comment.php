<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read', 'like:read', 'commentreply:read', 'warn:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['comment:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['comment:read'])]
    private ?\DateTimeInterface $last_edit = null;

    #[ORM\Column]
    #[Groups(['comment:read'])]
    private ?int $nb_edit = null;

    #[ORM\Column(length: 255)]
    #[Groups(['comment:read'])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: PostActu::class)]
    #[Groups(['comment:read'])]
    private ?postactu $post = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['comment:read'])]
    private ?user $user = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLastEdit(): ?\DateTime
    {
        return $this->last_edit;
    }

    public function setLastEdit(\DateTime $last_edit): static
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

    public function getPost(): ?postactu
    {
        return $this->post;
    }

    public function setPost(?postactu $post): static
    {
        $this->post = $post;

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
}
