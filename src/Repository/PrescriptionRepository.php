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

    public function findByDoctor(Doctor $doctor): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.doctor = :doctor')
            ->setParameter('doctor', $doctor)
            ->getQuery();

        return $qb->execute();
    }

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

    public function findByUserAndFilters($user, $confirmed, $order)
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

        if ($order === 'newest') {
            $qb->orderBy('p.creationDate', 'DESC');
        } elseif ($order === 'oldest') {
            $qb->orderBy('p.creationDate', 'ASC');
        }

        if ($confirmed !== null) {
            $qb->andWhere('p.confirmed = :confirmed')
                ->setParameter('confirmed', $confirmed);
        }

        return $qb->getQuery()->getResult();
    }
}