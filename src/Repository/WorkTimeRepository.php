<?php

namespace App\Repository;

use App\Entity\WorkTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkTime[]    findAll()
 * @method WorkTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkTime::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(WorkTime $entity, bool $flush = true): void
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
    public function remove(WorkTime $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
/**
 * @return WorkTimeSumUser [sum_work_time, project_id, p.name] Returns an array - employee working time in a given date range, summed up according to projects.
 */
    public function getUserDataWorkTimeSum(int $idUser, $dateStart, $dateEnd): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(wt.work_time))), '%H:%i') as sum_work_time,
        FORMAT(SUM((HOUR(wt.work_time)+MINUTE(wt.work_time)/60)*wt.cost_hour),2) as sum_cost,
        wt.project_id, p.name 
             FROM work_time wt 
             LEFT JOIN project p ON (wt.project_id = p.id)
             WHERE wt.user_id= :idUser AND wt.work_date>= :dateStart AND wt.work_date<= :dateEnd
             GROUP BY wt.project_id, p.name";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['idUser' => $idUser, 'dateStart'=> $dateStart, 'dateEnd' => $dateEnd]);
        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

/**
 * @return WorkTimeUser [work_date, work_time, project_id, name] Return an array - employee working time in a given date range.
 */
    public function getUserDataWorkTime (int $idUser, $dateStart, $dateEnd): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT wt.work_date, TIME_FORMAT(wt.work_time, '%H:%i') as work_time,
        FORMAT((HOUR(wt.work_time)+MINUTE(wt.work_time)/60)*wt.cost_hour,2) as sum_cost,
        wt.project_id, p.name, cost_hour
        FROM work_time wt 
        LEFT JOIN project p ON (wt.project_id = p.id)
        WHERE wt.user_id= :idUser AND wt.work_date>= :dateStart AND wt.work_date<= :dateEnd
        ORDER BY wt.work_date;";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['idUser' => $idUser, 'dateStart'=> $dateStart, 'dateEnd' => $dateEnd]);
        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

/**
 * @return WorkTimeSumProject [sum_work_time, project_id, p.name] Returns an array - employee working time in a given date range, summed up according to projects.
 */
public function getProjectDataWorkTimeSum(int $idProject, $dateStart, $dateEnd): array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(wt.work_time))), '%H:%i') as sum_work_time, 
    FORMAT(SUM((HOUR(wt.work_time)+MINUTE(wt.work_time)/60)*wt.cost_hour),2) as sum_cost,
    wt.user_id, u.first_name, u.last_name
    FROM work_time wt
    LEFT JOIN user u ON (wt.user_id = u.id)
    WHERE wt.project_id= :idProject AND wt.work_date>= :dateStart AND wt.work_date<= :dateEnd
    GROUP BY wt.user_id";
    $stmt = $conn->prepare($sql);
    $resultSet = $stmt->executeQuery(['idProject' => $idProject, 'dateStart'=> $dateStart, 'dateEnd' => $dateEnd]);
    // returns an array of arrays (i.e. a raw data set)
    return $resultSet->fetchAllAssociative();
}

/**
 * @return WorkTimeProject [sum_work_time, project_id, first_name, last_name] Returns an array - employee working time in a given date range, summed up according to projects.
 */
public function getProjectDataWorkTime(int $idProject, $dateStart, $dateEnd): array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = "SELECT wt.work_date, TIME_FORMAT(wt.work_time, '%H:%i') as work_time, 
    FORMAT((HOUR(wt.work_time)+MINUTE(wt.work_time)/60)*wt.cost_hour,2) as sum_cost,
    wt.project_id, u.first_name, u.last_name, wt.cost_hour
    FROM work_time wt
    LEFT JOIN user u ON (wt.user_id = u.id)
    WHERE wt.project_id= :idProject AND wt.work_date>= :dateStart AND wt.work_date<= :dateEnd
    ORDER BY wt.work_date";
    $stmt = $conn->prepare($sql);
    $resultSet = $stmt->executeQuery(['idProject' => $idProject, 'dateStart'=> $dateStart, 'dateEnd' => $dateEnd]);
    // returns an array of arrays (i.e. a raw data set)
    return $resultSet->fetchAllAssociative();
}

/**
 * @return WorkTimeEmploy [sum_work_time, user_id, first_name, last_name] Returns an array - employee working time in a given date range, summed up according to projects.
 */
public function getEmployDataWorkTimeSum(int $idEmploy, $dateStart, $dateEnd): array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(wt.work_time))), '%H:%i') as sum_work_time, 
    FORMAT(SUM((HOUR(wt.work_time)+MINUTE(wt.work_time)/60)*wt.cost_hour),2) as sum_cost,
    wt.user_id, u.first_name, u.last_name
    FROM work_time wt
    LEFT JOIN user u ON (wt.user_id = u.id)
    WHERE wt.employ_id= :idEmploy AND wt.work_date>= :dateStart AND wt.work_date<= :dateEnd
    GROUP BY wt.user_id";
    $stmt = $conn->prepare($sql);
    $resultSet = $stmt->executeQuery(['idEmploy' => $idEmploy, 'dateStart'=> $dateStart, 'dateEnd' => $dateEnd]);
    // returns an array of arrays (i.e. a raw data set)
    return $resultSet->fetchAllAssociative();
}

/**
 * @return WorkTimeEmploy [work_date, sum_work_time, project_id, name] Returns an array - employee working time in a given date range, summed up according to projects.
 */
public function getEmployDataWorkTime(int $idEmploy, $dateStart, $dateEnd): array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = "SELECT wt.work_date, TIME_FORMAT(wt.work_time, '%H:%i') as work_time, 
    FORMAT((HOUR(wt.work_time)+MINUTE(wt.work_time)/60)*wt.cost_hour,2) as sum_cost,
    wt.project_id, p.name, u.first_name, u.last_name, wt.cost_hour
    FROM work_time wt
    LEFT JOIN project p ON (wt.project_id = p.id)
    LEFT JOIN user u ON (wt.user_id = u.id)
    WHERE wt.employ_id= :idEmploy AND wt.work_date>= :dateStart AND wt.work_date<= :dateEnd
    ORDER BY wt.work_date";
    $stmt = $conn->prepare($sql);
    $resultSet = $stmt->executeQuery(['idEmploy' => $idEmploy, 'dateStart'=> $dateStart, 'dateEnd' => $dateEnd]);
    // returns an array of arrays (i.e. a raw data set)
    return $resultSet->fetchAllAssociative();
}

    // /**
    //  * @return WorkTime[] Returns an array of WorkTime objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkTime
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
