<?php

namespace App\Repository;

use App\Entity\Journeys;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Journeys|null find($id, $lockMode = null, $lockVersion = null)
 * @method Journeys|null findOneBy(array $criteria, array $orderBy = null)
 * @method Journeys[]    findAll()
 * @method Journeys[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourneysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Journeys::class);
    }

    // /**
    //  * @return Journeys[] Returns an array of Journeys objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Journeys
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
