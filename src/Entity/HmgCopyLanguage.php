<?php

namespace App\Entity;

use App\Repository\HmgCopyLanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HmgCopyLanguageRepository::class)]
class HmgCopyLanguage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['copyLanguage:read', 'historygame:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['copyLanguage:read', 'historygame:read'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: HmgCopy::class, mappedBy: 'language')]
    #[Groups(['copyLanguage:read'])]
    private Collection $hmgCopies;

    public function __construct()
    {
        $this->hmgCopies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, HmgCopy>
     */
    public function getHmgCopies(): Collection
    {
        return $this->hmgCopies;
    }

    public function addHmgCopy(HmgCopy $hmgCopy): static
    {
        if (!$this->hmgCopies->contains($hmgCopy)) {
            $this->hmgCopies->add($hmgCopy);
            $hmgCopy->addLanguage($this);
        }

        return $this;
    }

    public function removeHmgCopy(HmgCopy $hmgCopy): static
    {
        if ($this->hmgCopies->removeElement($hmgCopy)) {
            $hmgCopy->removeLanguage($this);
        }

        return $this;
    }
}
