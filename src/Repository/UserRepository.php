<?php

namespace App\Repository;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Pharmacy;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findIdByEmail(string $email): ?int
    {
        $patient = $this->createQueryBuilder('p')
            ->select('p.id')
            ->where('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();

        return $patient ? $patient['id'] : null;
    }

    public function getUsersWithEmail(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT COUNT(*)
        FROM user
        WHERE type IN ("doctor", "patient", "pharmacy")
        AND email IS NOT NULL
    ';

        try {
            return $conn->executeQuery($sql)->fetchOne();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getUnverifiedUsers(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT COUNT(*)
        FROM user
        WHERE type IN ("doctor", "patient", "pharmacy")
        AND email IS NOT NULL
        AND is_verified = FALSE
    ';
        return (int)$conn->executeQuery($sql)->fetchOne();
    }
    public function getUsersWithNumber(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT COUNT(*)
        FROM user
        WHERE type IN ("doctor", "patient", "pharmacy")
        AND number IS NOT NULL
    ';

        try {
            return $conn->executeQuery($sql)->fetchOne();
        } catch (\Exception $e) {
            return 0;
        }
    }
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
