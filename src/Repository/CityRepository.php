<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findAllArray()
    {
        $query = $this
            ->createQueryBuilder('c')
            ->select('c');
        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return City[]
     */
    public function findSearch($search): array
    {
        $query = $this
            ->createQueryBuilder('citys');

        if (!empty($search)) {
            $query = $query
                ->andWhere('citys.name LIKE :toSearch')
                ->setParameter('toSearch', "%{$search}%");
        }
        $query = $query
            ->getQuery()
            ->getResult();

        return $query;
    }
}
