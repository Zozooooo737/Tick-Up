<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function getTotalReservedPlaces(): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('SUM(b.places)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalRevenue(): float
    {
        return (float) $this->createQueryBuilder('b')
            ->select('SUM(b.places * b.pricePerPerson)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
