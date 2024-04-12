<?php

namespace App\Repository;

use App\Entity\IssueResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IssueResponse>
 *
 * @method IssueResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method IssueResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method IssueResponse[]    findAll()
 * @method IssueResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IssueResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IssueResponse::class);
    }

//    /**
//     * @return IssueResponse[] Returns an array of IssueResponse objects
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

//    public function findOneBySomeField($value): ?IssueResponse
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findAllWithIssues(): array
    {
        return $this->createQueryBuilder('ir')
            ->addSelect('i') // Add the issue association
            ->leftJoin('ir.issue', 'i')
            ->getQuery()
            ->getResult();
    }
}
