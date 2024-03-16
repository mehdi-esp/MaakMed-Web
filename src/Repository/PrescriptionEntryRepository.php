<?php

namespace App\Repository;

use App\Entity\PrescriptionEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrescriptionEntry>
 *
 * @method PrescriptionEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrescriptionEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrescriptionEntry[]    findAll()
 * @method PrescriptionEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrescriptionEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionEntry::class);
    }

//    /**
//     * @return PrescriptionEntry[] Returns an array of PrescriptionEntry objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PrescriptionEntry
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
