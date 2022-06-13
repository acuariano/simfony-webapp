<?php

namespace App\Entity;

use App\Repository\DrinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrinkRepository::class)]
class Drink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $Type;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $Alcohol;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $Hot;

    #[ORM\OneToMany(mappedBy: 'Drink', targetEntity: Scientist::class)]
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

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(?string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function isAlcohol(): ?bool
    {
        return $this->Alcohol;
    }

    public function setAlcohol(?bool $Alcohol): self
    {
        $this->Alcohol = $Alcohol;

        return $this;
    }

    public function isHot(): ?bool
    {
        return $this->Hot;
    }

    public function setHot(?bool $Hot): self
    {
        $this->Hot = $Hot;

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
            $scientist->setDrink($this);
        }

        return $this;
    }

    public function removeScientist(Scientist $scientist): self
    {
        if ($this->scientists->removeElement($scientist)) {
            // set the owning side to null (unless already changed)
            if ($scientist->getDrink() === $this) {
                $scientist->setDrink(null);
            }
        }

        return $this;
    }
}
