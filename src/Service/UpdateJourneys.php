<?php

namespace App\Service;


use App\Entity\Journeys;
use App\Entity\Status;
use App\Entity\User;
use App\Repository\JourneysRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;

class UpdateJourneys extends AbstractController{

    private $journeysRepository;
    private $statusRepository;
    private $entityManager;
    private $userRepository;

    public function __construct(JourneysRepository $journeysRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->journeysRepository = $journeysRepository;
        $this->statusRepository = $statusRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
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

    public function updateUserJourney(User $user) {

        $journeys = $user->getOwner();
        $unknownUser = $this->userRepository->findOneBy(array("username"=>"inconnu"));
        $cancelled = $this->statusRepository->findOneBy(array("name"=>"Annulée"));

        foreach ($journeys as $journey) {
            $journey->setUser($unknownUser);

            if($journey->getStatus()->getName() == "Activité en cours" || $journey->getStatus()->getName() == "Passée" || $journey->getStatus()->getName() == "Annulée") {

            } else {
                $journey->setStatus($cancelled);
            }

            $this->entityManager->persist($journey);
            $this->entityManager->flush();
        }
    }
}