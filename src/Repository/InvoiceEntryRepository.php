<?php

namespace App\Repository;

use App\Entity\InvoiceEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoiceEntry>
 *
 * @method InvoiceEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceEntry[]    findAll()
 * @method InvoiceEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceEntry::class);
    }

//    /**
//     * @return InvoiceEntry[] Returns an array of InvoiceEntry objects
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

//    public function findOneBySomeField($value): ?InvoiceEntry
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
