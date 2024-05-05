<?php

namespace App\Repository;

use App\Entity\Doctor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Doctor::class);
    }
    public function getDoctorSpecializationDistribution(): array
    {
        $queryBuilder = $this->createQueryBuilder('d');

        $queryBuilder->select('d.specialization, COUNT(d.id) as count')
            ->groupBy('d.specialization');

        return $queryBuilder->getQuery()->getResult();
    }
}