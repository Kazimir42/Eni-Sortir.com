<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatusRepository::class)
 */
class Status
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
     * @ORM\OneToMany(targetEntity=Journeys::class, mappedBy="status")
     */
    private $Journey;

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
            $journey->setStatus($this);
        }

        return $this;
    }

    public function removeJourney(Journeys $journey): self
    {
        if ($this->Journey->removeElement($journey)) {
            // set the owning side to null (unless already changed)
            if ($journey->getStatus() === $this) {
                $journey->setStatus(null);
            }
        }

        return $this;
    }
}
