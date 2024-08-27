<?php

namespace App\Repository;

use App\Entity\UserQualification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserQualification|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserQualification|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserQualification[]    findAll()
 * @method UserQualification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserQualificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserQualification::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UserQualification $entity, bool $flush = true): void
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
    public function remove(UserQualification $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remaindBefore(): array
    {
        $sql = "SELECT 
        u.first_name,
        u.last_name,
        q.name as name_qualification,
        uq.date_start,
        uq.date_end,
        uq.type
    FROM 
        user_qualification uq
    LEFT JOIN qualification q ON uq.qualification_id = q.id
    LEFT JOIN user u ON uq.user_id = u.id
    WHERE 
        (DATE_SUB(uq.date_end, INTERVAL q.remind_days_before DAY) = CURDATE()
        OR DATE_SUB(uq.date_end, INTERVAL q.remind_days_before / 2 DAY) = CURDATE()
        OR DATE_SUB(uq.date_end, INTERVAL 1 DAY) = CURDATE()
        OR uq.date_end = CURDATE())
        AND q.remind_days_before IS NOT NULL";
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function remaindBeforeUser($idUser): array
    {
        $sql = "SELECT 
        u.first_name,
        u.last_name,
        q.name as name_qualification,
        uq.date_start,
        uq.date_end,
        uq.type
    FROM 
        user_qualification uq
    LEFT JOIN qualification q ON uq.qualification_id = q.id
    LEFT JOIN user u ON uq.user_id = u.id
    WHERE 
        (DATE_SUB(uq.date_end, INTERVAL q.remind_days_before DAY) = CURDATE()
        OR DATE_SUB(uq.date_end, INTERVAL q.remind_days_before / 2 DAY) = CURDATE()
        OR DATE_SUB(uq.date_end, INTERVAL 1 DAY) = CURDATE()
        OR uq.date_end = CURDATE())
        AND q.remind_days_before IS NOT NULL
        AND u.id = :idUser";
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['idUser' => $idUser]);
        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    // /**
    //  * @return UserQualification[] Returns an array of UserQualification objects
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
    public function findOneBySomeField($value): ?UserQualification
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
