<?php

namespace App\Entity;

use App\Repository\HmgCopyFormatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HmgCopyFormatRepository::class)]
class HmgCopyFormat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['historygame:read', 'copyFormat:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['historygame:read', 'copyFormat:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: HmgCopy::class, mappedBy: 'format')]
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
            $hmgCopy->setFormat($this);
        }

        return $this;
    }

    public function removeHmgCopy(HmgCopy $hmgCopy): static
    {
        if ($this->hmgCopies->removeElement($hmgCopy)) {
            // set the owning side to null (unless already changed)
            if ($hmgCopy->getFormat() === $this) {
                $hmgCopy->setFormat(null);
            }
        }

        return $this;
    }
}
