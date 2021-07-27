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
        $query = $this
            ->createQueryBuilder('journeys')
            ->select('college', 'journeys', 'user')
            ->join('journeys.college', 'college')
            ->join('journeys.user', 'user');


        if (!empty($searchData->college)){
            $query = $query
                ->andWhere('college.name = :college')
                ->setParameter('college', $searchData->college);
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


}
