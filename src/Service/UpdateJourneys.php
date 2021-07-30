<?php

namespace App\Service;


use App\Entity\Journeys;
use App\Entity\Status;
use App\Repository\JourneysRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;

class UpdateJourneys extends AbstractController{

    private $journeysRepository;
    private $statusRepository;
    private $entityManager;

    public function __construct(JourneysRepository $journeysRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManager)
    {
        $this->journeysRepository = $journeysRepository;
        $this->statusRepository = $statusRepository;
        $this->entityManager = $entityManager;
    }

    public function updateEndedRegisterDate(){

        //CURRENT DATE AT MIDNIGHT
        $time = new \DateTime();
        $time->format('d/m/Y');
        $time->settime(0,0, 0);

        $statusEnded = $this->statusRepository->findOneBy(array('name' => "Clôturée"));

        $journeys = $this->journeysRepository->findBy(array('deadlineDate' => $time));

        foreach ($journeys as $journey){
            if($journey->getStatus()->getName() == "Créée" || $journey->getStatus()->getName() == "Ouverte" ){
                $journey->setStatus($statusEnded);
                $this->entityManager->persist($journey);
                $this->entityManager->flush();
            }
        }

    }


    public function updateStatusJourney(){

        //CURRENT DATE AT MIDNIGHT
        $time = new \DateTime();
        $time->format('d/m/Y');

        $statusCurrently = $this->statusRepository->findOneBy(array('name' => "Activité en cours"));
        $statusEnd = $this->statusRepository->findOneBy(array('name' => "Passée"));

        $journeys = $this->journeysRepository->findAllInCourseJourneys();


        foreach ($journeys as $journey){

            $journeyStart = $journey->getStartingDate();
            $minutesToAdd = $journey->getDuration();
            $journeyEndTime = $journeyStart->modify("+{$minutesToAdd} minutes");


            if($journey->getStatus()->getName() == "Clôturée" || $journey->getStatus()->getName() == "Activité en cours"){


                if($journeyEndTime > $time){ //JOURNEY IS IN CURSE

                    $journey->setStatus($statusCurrently);
                    $this->entityManager->persist($journey);
                    $this->entityManager->flush();
                }else{//JOURNEY IS END

                    $journey->setStatus($statusEnd);
                    $this->entityManager->persist($journey);
                    $this->entityManager->flush();
                }
            }
            $journeyStart->modify("-{$minutesToAdd} minutes");

        }

    }


}