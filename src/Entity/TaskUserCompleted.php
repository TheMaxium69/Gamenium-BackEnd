<?php

namespace App\Entity;

use App\Repository\TaskUserCompletedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskUserCompletedRepository::class)]
class TaskUserCompleted
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TaskUser $taskuser = null;

    #[ORM\ManyToOne(inversedBy: 'completed_at')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $completed_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskuser(): ?TaskUser
    {
        return $this->taskuser;
    }

    public function setTaskuser(?TaskUser $taskuser): static
    {
        $this->taskuser = $taskuser;

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

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completed_at;
    }

    public function setCompletedAt(\DateTimeImmutable $completed_at): static
    {
        $this->completed_at = $completed_at;

        return $this;
    }
}
