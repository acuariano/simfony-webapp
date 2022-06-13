<?php

namespace App\Entity;

use App\Repository\ScientistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScientistRepository::class)]
class Scientist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Nationality;

    #[ORM\ManyToOne(targetEntity: Pet::class, inversedBy: 'scientists')]
    private $Pet;

    #[ORM\ManyToOne(targetEntity: Drink::class, inversedBy: 'scientists')]
    private $Drink;

    #[ORM\ManyToOne(targetEntity: House::class, inversedBy: 'scientists')]
    private $House;

    #[ORM\ManyToOne(targetEntity: Cigarettes::class, inversedBy: 'scientists')]
    private $Cigarettes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNationality(): ?string
    {
        return $this->Nationality;
    }

    public function setNationality(?string $Nationality): self
    {
        $this->Nationality = $Nationality;

        return $this;
    }

    public function getPet(): ?Pet
    {
        return $this->Pet;
    }

    public function setPet(?Pet $Pet): self
    {
        $this->Pet = $Pet;

        return $this;
    }

    public function getDrink(): ?Drink
    {
        return $this->Drink;
    }

    public function setDrink(?Drink $Drink): self
    {
        $this->Drink = $Drink;

        return $this;
    }

    public function getHouse(): ?House
    {
        return $this->House;
    }

    public function setHouse(?House $House): self
    {
        $this->House = $House;

        return $this;
    }

    public function getCigarettes(): ?Cigarettes
    {
        return $this->Cigarettes;
    }

    public function setCigarettes(?Cigarettes $Cigarettes): self
    {
        $this->Cigarettes = $Cigarettes;

        return $this;
    }
}
