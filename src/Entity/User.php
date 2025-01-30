<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['useradmin:read', 'comment:read', 'screenshot:read' , 'historygame:read' , 'like:read', 'picture:read', 'post:read', 'userRate:read', 'badgesversuser:read', 'user:read', 'followProvider:read', 'followPageGame:read', 'follow:read', 'taskusercompleted:read', 'view:read', 'commentreply:read', 'default:read', 'historyplateform:read', 'warn:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['useradmin:read', 'user:read', 'comment:read', 'commentreply:read'])]
    private ?int $id_useritium = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    #[Groups(['useradmin:read', 'user:read', 'comment:read', 'commentreply:read'])]
    private ?\DateTimeImmutable $joinAt = null;

    #[ORM\Column]
    #[Groups(['useradmin:read', 'user:read'])]
    private ?\DateTimeImmutable $lastConnection = null;

    #[ORM\Column]
    #[Groups(['useradmin:read'])]
    private array $ip = [];

    #[ORM\Column(length: 255)]
    #[Groups(['useradmin:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['useradmin:read', 'user:read', 'comment:read', 'commentreply:read'])]
    private ?string $displayname_useritium = null;

    #[ORM\Column(length: 255)]
    #[Groups(['useradmin:read', 'default:read', 'screenshot:read', 'user:read', 'comment:read', 'historygame:read','taskusercompleted:read', 'view:read', 'commentreply:read', 'historyplateform:read'])]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(['useradmin:read', 'user:read','comment:read', 'commentreply:read', 'warn:read'])]
    private ?string $displayname = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $token = null;

    #[ORM\ManyToOne(cascade: ['persist'], targetEntity: Picture::class)]
    #[Groups(['useradmin:read', 'user:read', 'comment:read', 'commentreply:read'])]
    private ?picture $pp = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['useradmin:read', 'user:read', 'comment:read', 'commentreply:read'])]
    private ?string $color = null;

    #[ORM\OneToMany(targetEntity: Log::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $logs;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }


    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUseritium(): ?int
    {
        return $this->id_useritium;
    }

    public function setIdUseritium(int $id_useritium): static
    {
        $this->id_useritium = $id_useritium;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';
        
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getJoinAt(): ?\DateTimeImmutable
    {
        return $this->joinAt;
    }

    public function setJoinAt(\DateTimeImmutable $joinAt): static
    {
        $this->joinAt = $joinAt;

        return $this;
    }

    public function getLastConnection(): ?\DateTimeImmutable
    {
        return $this->lastConnection;
    }

    public function setLastConnection(\DateTimeImmutable $lastConnection): static
    {
        $this->lastConnection = $lastConnection;

        return $this;
    }



    public function getIp(): array
    {
        return $this->ip;
    }

    public function setIp(array $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getDisplaynameUseritium(): ?string
    {
        return $this->displayname_useritium;
    }

    public function setDisplaynameUseritium(?string $displayname_useritium): static
    {
        $this->displayname_useritium = $displayname_useritium;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getDisplayname(): ?string
    {
        return $this->displayname;
    }

    public function setDisplayname(string $displayname): static
    {
        $this->displayname = $displayname;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getPp(): ?picture
    {
        return $this->pp;
    }

    public function setPp(?picture $pp): static
    {
        $this->pp = $pp;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection<int, Log>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): static
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): static
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }

        return $this;
    }
}
