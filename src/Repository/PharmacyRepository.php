<?php

namespace App\Repository;

use App\Entity\Pharmacy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pharmacy>
 */
class PharmacyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pharmacy::class);
    }
    public function getPharmacyAddressDistribution(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.address as address, COUNT(p.id) as count')
            ->groupBy('p.address')
            ->getQuery()
            ->getResult();
    }
}