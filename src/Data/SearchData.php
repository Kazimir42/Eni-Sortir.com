<?php


namespace App\Data;


class SearchData
{

    /**
     * @var string
     */
    public $college = '';

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

}