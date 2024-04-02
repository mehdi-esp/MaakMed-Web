<?php

namespace App\Repository;

use App\Entity\Prescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\{Doctor, Patient};

/**
 * @extends ServiceEntityRepository<Prescription>
 *
 * @method Prescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prescription[]    findAll()
 * @method Prescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescription::class);
    }
    // In PrescriptionRepository.php

public function findByDoctor(Doctor $doctor): array
{
    $qb = $this->createQueryBuilder('p')
        ->where('p.doctor = :doctor')
        ->setParameter('doctor', $doctor)
        ->getQuery();

    return $qb->execute();
}
// in PrescriptionRepository.php

// in PrescriptionRepository.php

// in PrescriptionRepository.php

  public function findByUser($user)
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.visit', 'v');

        if ($user instanceof Doctor) {
            $qb->where('v.doctor = :user')
                ->setParameter('user', $user);
        } elseif ($user instanceof Patient) {
            $qb->where('v.patient = :user')
                ->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }
//    /**
//     * @return Prescription[] Returns an array of Prescription objects
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

//    public function findOneBySomeField($value): ?Prescription
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
