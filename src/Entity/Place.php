<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaceRepository::class)
 */
class Place
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Street;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $x;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $y;

    /**
     * @ORM\OneToMany(targetEntity=Journeys::class, mappedBy="place")
     */
    private $Journey;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="place")
     */
    private $city;

    public function __construct()
    {
        $this->Journey = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->Street;
    }

    public function setStreet(string $Street): self
    {
        $this->Street = $Street;

        return $this;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function setX(?float $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function setY(?float $y): self
    {
        $this->y = $y;

        return $this;
    }

    /**
     * @return Collection|Journeys[]
     */
    public function getJourney(): Collection
    {
        return $this->Journey;
    }

    public function addJourney(Journeys $journey): self
    {
        if (!$this->Journey->contains($journey)) {
            $this->Journey[] = $journey;
            $journey->setPlace($this);
        }

        return $this;
    }

    public function removeJourney(Journeys $journey): self
    {
        if ($this->Journey->removeElement($journey)) {
            // set the owning side to null (unless already changed)
            if ($journey->getPlace() === $this) {
                $journey->setPlace(null);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
