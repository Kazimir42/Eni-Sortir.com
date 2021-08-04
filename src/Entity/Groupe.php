<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupeRepository::class)
 */
class Groupe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="groupe_owner")
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity=user::class, inversedBy="groupes")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity=Journeys::class, mappedBy="groupe")
     */
    private $journeys;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->journeys = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?user
    {
        return $this->owner;
    }

    public function setOwner(?user $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(user $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(user $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    /**
     * @return Collection|Journeys[]
     */
    public function getJourneys(): Collection
    {
        return $this->journeys;
    }

    public function addJourney(Journeys $journey): self
    {
        if (!$this->journeys->contains($journey)) {
            $this->journeys[] = $journey;
            $journey->setGroupe($this);
        }

        return $this;
    }

    public function removeJourney(Journeys $journey): self
    {
        if ($this->journeys->removeElement($journey)) {
            // set the owning side to null (unless already changed)
            if ($journey->getGroupe() === $this) {
                $journey->setGroupe(null);
            }
        }

        return $this;
    }
}
