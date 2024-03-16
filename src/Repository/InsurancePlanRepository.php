<?php

namespace App\Repository;

use App\Entity\InsurancePlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InsurancePlan>
 *
 * @method InsurancePlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method InsurancePlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method InsurancePlan[]    findAll()
 * @method InsurancePlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InsurancePlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsurancePlan::class);
    }

//    /**
//     * @return InsurancePlan[] Returns an array of InsurancePlan objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InsurancePlan
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
