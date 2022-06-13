<?php

namespace App\Entity;

use App\Repository\CigarettesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CigarettesRepository::class)]
class Cigarettes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Brand;

    #[ORM\OneToMany(mappedBy: 'Cigarettes', targetEntity: Scientist::class)]
    private $scientists;

    public function __construct()
    {
        $this->scientists = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getBrand();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->Brand;
    }

    public function setBrand(?string $Brand): self
    {
        $this->Brand = $Brand;

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
            $scientist->setCigarettes($this);
        }

        return $this;
    }

    public function removeScientist(Scientist $scientist): self
    {
        if ($this->scientists->removeElement($scientist)) {
            // set the owning side to null (unless already changed)
            if ($scientist->getCigarettes() === $this) {
                $scientist->setCigarettes(null);
            }
        }

        return $this;
    }
}
