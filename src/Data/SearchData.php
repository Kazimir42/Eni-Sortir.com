<?php


namespace App\Data;


use App\Entity\College;
use Doctrine\ORM\Mapping as ORM;

class SearchData
{

    /**
     * @ORM\ManyToOne(targetEntity=College::class, inversedBy="User")
     */
    private $college;

    /**
     * @var string
     */
    public $toSearch = '';

    /**
     * @var(type="datetime")
     */
    private $startDate;

    /**
     * @var(type="datetime")
     */
    private $endDate;

    /**
     * @var bool
     */
    public $isOwner = false;

    /**
     * @var bool
     */
    public $ameIInscrit = false;

    /**
     * @var bool
     */
    public $ameIUninscrit = false;

    /**
     * @var bool
     */
    public $journeysPassed = false;






    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }


    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }



    public function getCollege(): ?College
    {
        return $this->college;
    }

    public function setCollege(?College $college): self
    {
        $this->college = $college;

        return $this;
    }


}