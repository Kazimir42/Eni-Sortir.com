<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Journeys;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Journeys|null find($id, $lockMode = null, $lockVersion = null)
 * @method Journeys|null findOneBy(array $criteria, array $orderBy = null)
 * @method Journeys[]    findAll()
 * @method Journeys[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourneysRepository extends ServiceEntityRepository
{

    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Journeys::class);
        $this->security = $security;
    }

    public function findSearch(SearchData $searchData){

        $now = new \DateTime();
        $now->modify('-30 days');


        $query = $this
            ->createQueryBuilder('journeys')
            ->select('college', 'journeys', 'user')
            ->join('journeys.college', 'college')
            ->join('journeys.user', 'user')
            ->andWhere('journeys.startingDate >= :now') //get journeys in last 30 days
            ->setParameter('now', $now);


        if (!empty($searchData->getCollege())){
            $query = $query
                ->andWhere('college.name = :college')
                ->setParameter('college', $searchData->getCollege()->getName());
        }

        if (!empty($searchData->toSearch)){
            $query = $query
                ->andWhere('journeys.name LIKE :toSearch')
                ->setParameter('toSearch',"%{$searchData->toSearch}%");
        }

        if (!empty($searchData->getStartDate())){
            $query = $query
                ->andWhere('journeys.startingDate > :startDate')
                ->setParameter('startDate',$searchData->getStartDate());
        }

        if (!empty($searchData->getEndDate())){
            $query = $query
                ->andWhere('journeys.deadlineDate < :endDate')
                ->setParameter('endDate',$searchData->getEndDate());
        }

        //LEGIT ??
        $user = $this->security->getUser();

        if ($searchData->isOwner){
            $query = $query
                ->andWhere('journeys.user = :owner')
                ->setParameter('owner', $user->getId());
        }

        if ($searchData->ameIInscrit){
            $query = $query
                ->andWhere(':user MEMBER OF journeys.users')
                ->setParameter('user', $user->getId());
        }

        if ($searchData->ameIUninscrit){
            $query = $query
                ->andWhere(':user NOT MEMBER OF journeys.users')
                ->setParameter('user', $user->getId());
        }

        if ($searchData->journeysPassed){
            $query = $query
                ->andWhere('journeys.status = :status')
                ->setParameter('status', 5);
        }


        $query = $query
            ->getQuery()
            ->getResult();


        return $query;
    }



    public function findAllInCourseJourneys(){

        $now = new \DateTime();

        $query = $this
            ->createQueryBuilder('j')
            ->where('j.startingDate <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();

        return $query;
    }


    public function findAllInLast30Days(){

        $now = new \DateTime();
        $now->modify('+30 days');

        $query = $this
            ->createQueryBuilder('j')
            ->where('j.startingDate <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();

        return $query;
    }


}
