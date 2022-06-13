<?php

namespace App\Entity;

use App\Repository\PetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PetRepository::class)]
class Pet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Species;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Breed;

    #[ORM\OneToMany(mappedBy: 'Pet', targetEntity: Scientist::class)]
    private $scientists;

    public function __construct()
    {
        $this->scientists = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getType();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpecies(): ?string
    {
        return $this->Species;
    }

    public function setSpecies(?string $Species): self
    {
        $this->Species = $Species;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(?string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->Breed;
    }

    public function setBreed(?string $Breed): self
    {
        $this->Breed = $Breed;

        return $this;
    }

    /**
     * @return Collection<int, Scientist>
     */
    public function getScientists(): Collection
    {
        return $this->scientists;
    }

    public function addScientist(Scientist $scientist): self
    {
        if (!$this->scientists->contains($scientist)) {
            $this->scientists[] = $scientist;
            $scientist->setPet($this);
        }

        return $this;
    }

    public function removeScientist(Scientist $scientist): self
    {
        if ($this->scientists->removeElement($scientist)) {
            // set the owning side to null (unless already changed)
            if ($scientist->getPet() === $this) {
                $scientist->setPet(null);
            }
        }

        return $this;
    }
}
