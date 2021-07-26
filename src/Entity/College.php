<?php

namespace App\Entity;

use App\Repository\CollegeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CollegeRepository::class)
 */
class College
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
     * @ORM\OneToMany(targetEntity=Journeys::class, mappedBy="college")
     */
    private $Journeys;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="college")
     */
    private $User;

    public function __construct()
    {
        $this->Journeys = new ArrayCollection();
        $this->User = new ArrayCollection();
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
    public function getJourneys(): Collection
    {
        return $this->Journeys;
    }

    public function addJourney(Journeys $journey): self
    {
        if (!$this->Journeys->contains($journey)) {
            $this->Journeys[] = $journey;
            $journey->setCollege($this);
        }

        return $this;
    }

    public function removeJourney(Journeys $journey): self
    {
        if ($this->Journeys->removeElement($journey)) {
            // set the owning side to null (unless already changed)
            if ($journey->getCollege() === $this) {
                $journey->setCollege(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(User $user): self
    {
        if (!$this->User->contains($user)) {
            $this->User[] = $user;
            $user->setCollege($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->User->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCollege() === $this) {
                $user->setCollege(null);
            }
        }

        return $this;
    }
}
