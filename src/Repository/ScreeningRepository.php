<?php

namespace App\Repository;

use App\Entity\Screening;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Screening>
 */
class ScreeningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Screening::class);
    }

    public function findScreeningsFromMovie($movie): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.movie = :movie')
            ->setParameter('movie', $movie)
            ->orderBy('s.dateTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
