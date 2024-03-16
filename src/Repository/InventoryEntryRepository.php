<?php

namespace App\Repository;

use App\Entity\InventoryEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventoryEntry>
 *
 * @method InventoryEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method InventoryEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method InventoryEntry[]    findAll()
 * @method InventoryEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InventoryEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryEntry::class);
    }

//    /**
//     * @return InventoryEntry[] Returns an array of InventoryEntry objects
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

//    public function findOneBySomeField($value): ?InventoryEntry
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
