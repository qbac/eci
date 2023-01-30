<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Get Active User except Admin
     */
    public function getActiveUser()
    {
        $sql = "SELECT u.id, u.first_name as FirstName, u.last_name as LastName, u.email, (SELECT MAX(wt2.work_date) FROM work_time wt2 WHERE wt2.user_id = u.id) as maxWorkDate, e.name as EmployName, u.cost_hour as costHour, p.name as projectName FROM user u 
        LEFT JOIN work_time wt ON u.id = wt.user_id
        LEFT JOIN employ e ON u.employ_id = e.id
        LEFT JOIN project p ON wt.project_id = p.id
        where u.active=1 
        AND u.email <> 'admin@elbitech.pl'
        GROUP BY u.id";
    $conn = $this->getEntityManager()->getConnection();
    $stmt = $conn->prepare($sql);
    $resultSet = $stmt->executeQuery();
    // returns an array of arrays (i.e. a raw data set)
    return $resultSet->fetchAllAssociative();
    }

        /**
     * Get Active User except Admin
     */
    public function getUnactiveUser()
    {
        $qb = $this->createQueryBuilder('u')
        ->where('u.active = 0')
        ->andWhere("u.email <> 'admin@elbitech.pl'");
        $query = $qb->getQuery();
        return $query->execute();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
