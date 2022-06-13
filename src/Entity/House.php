<?php

namespace App\Entity;

use App\Repository\HouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseRepository::class)]
class House
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Color;

    #[ORM\OneToMany(mappedBy: 'House', targetEntity: Scientist::class)]
    private $scientists;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Number;

    public function __construct()
    {
        $this->scientists = new ArrayCollection();
    }


    public function __toString(): string
    {
        return "{$this->getNumber()} {$this->getColor()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColor(): ?string
    {
        return $this->Color;
    }

    public function setColor(?string $Color): self
    {
        $this->Color = $Color;

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
            $scientist->setHouse($this);
        }

        return $this;
    }

    public function removeScientist(Scientist $scientist): self
    {
        if ($this->scientists->removeElement($scientist)) {
            // set the owning side to null (unless already changed)
            if ($scientist->getHouse() === $this) {
                $scientist->setHouse(null);
            }
        }

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->Number;
    }

    public function setNumber(?int $Number): self
    {
        $this->Number = $Number;

        return $this;
    }
}
