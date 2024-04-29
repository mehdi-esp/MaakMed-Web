<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 *
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }
    public function getActiveUsersPercentage(): float
        {
            $totalUsers = $this->getTotalUsersCount();
            if ($totalUsers === 0) {
                return 0.0;
            }

            $activeUsers = $this->createQueryBuilder('s')
                ->select('COUNT(s.id)')
                ->where('s.status = :status')
                ->setParameter('status', 'active')
                ->getQuery()
                ->getSingleScalarResult();

            return ($activeUsers / $totalUsers) * 100.0;
        }
        public function getCancelingUsersPercentage(): float
            {
                $totalUsers = $this->getTotalUsersCount();
                if ($totalUsers === 0) {
                    return 0.0; // Avoid division by zero
                }

                $cancelingUsers = $this->createQueryBuilder('s')
                    ->select('COUNT(s.id)')
                    ->where('s.status = :status')
                    ->setParameter('status', 'canceling')
                    ->getQuery()
                    ->getSingleScalarResult();

                return ($cancelingUsers / $totalUsers) * 100.0;
            }
        private function getTotalUsersCount(): int
        {
            return $this->createQueryBuilder('s')
                ->select('COUNT(s.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

   public function getPlanWithMostSubscribers(): ?array
       {
           return $this->createQueryBuilder('s')
               ->select('p.name as plan,p.cost as cost, COUNT(s.id) as count')
               ->join('s.plan', 'p')
               ->where('s.status = :status')
               ->setParameter('status', 'active')
               ->groupBy('p.name')
                ->addGroupBy('p.cost')
               ->orderBy('count', 'DESC')
               ->setMaxResults(1)
               ->getQuery()
               ->getOneOrNullResult();
       }
       public function getTotalActiveSubscribers(): int
       {
           return $this->createQueryBuilder('s')
               ->select('COUNT(s.id) as count')
               ->where('s.status = :status')
               ->setParameter('status', 'active')
               ->getQuery()
               ->getSingleScalarResult();
       }
        public function getTotalCancelingSubscribers(): int
              {
                  return $this->createQueryBuilder('s')
                      ->select('COUNT(s.id) as count')
                      ->where('s.status = :status')
                      ->setParameter('status', 'canceling')
                      ->getQuery()
                      ->getSingleScalarResult();
              }


    public function getTotalRevenuePerPlan()
       {
           $currentYearStart = new \DateTime(date('Y-01-01'));
           $qb = $this->getEntityManager()->createQueryBuilder();

           $result = $qb->select('p.name as plan, p.cost as cost, COUNT(s.id) as count')
               ->from('App\Entity\Subscription', 's')
               ->join('s.plan', 'p')
               ->where($qb->expr()->andX(
                   $qb->expr()->neq('s.status', ':status'),
                   $qb->expr()->gte('s.startDate', ':startDate')
               ))
               ->setParameter('status', 'pending')
               ->setParameter('startDate', $currentYearStart)
               ->groupBy('p.name')
               ->addGroupBy('p.cost')
               ->getQuery()
               ->getResult();

           $revenueData = [];
           foreach ($result as $row) {
               $totalRevenue = $row['cost'] * $row['count'];
               $revenueData[] = [
                   'plan' => $row['plan'],
                   'cost' => $row['cost'],
                   'totalRevenue' => $totalRevenue,
               ];
           }

           return $revenueData;
       }

       public function getPlanWithMostCanceledStatus(): ?array
       {
           return $this->createQueryBuilder('s')
               ->select('p.name as plan, COUNT(s.id) as count')
               ->join('s.plan', 'p')
               ->where('s.status = :status')
               ->setParameter('status', 'canceled')
               ->groupBy('p.name')

               ->orderBy('count', 'DESC')
               ->setMaxResults(1)
               ->getQuery()
               ->getOneOrNullResult();
       }

       public function getPlanWithMostCancelingStatus(): ?array
       {
           return $this->createQueryBuilder('s')
               ->select('p.name as plan, COUNT(s.id) as count')
               ->join('s.plan', 'p')
               ->where('s.status = :status')
               ->setParameter('status', 'canceling')
               ->groupBy('p.name')
               ->orderBy('count', 'DESC')
               ->setMaxResults(1)
               ->getQuery()
               ->getOneOrNullResult();
       }
//    /**
//     * @return Subscription[] Returns an array of Subscription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Subscription
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
