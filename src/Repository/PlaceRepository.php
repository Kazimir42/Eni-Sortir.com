<?php

namespace App\Repository;

use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    public function findAllArray()
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p');
        return $query->getQuery()->getArrayResult();
    }

    public function findAllByIdArray($id)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->andWhere('p.city = :id')
            ->setParameter('id',$id);
        return $query->getQuery()->getArrayResult();
    }

    public function findOneById($id)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->andWhere('p.id = :id')
            ->setParameter('id',$id);
        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return Place[]
     */
    public function findSearch($search): array
    {
        $query = $this
            ->createQueryBuilder('places');

        if (!empty($search)) {
            $query = $query
                ->andWhere('places.name LIKE :toSearch')
                ->setParameter('toSearch', "%{$search}%");
        }
        $query = $query
            ->getQuery()
            ->getResult();

        return $query;
    }
}
