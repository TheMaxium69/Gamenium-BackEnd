<?php

namespace App\Repository;

use App\Entity\TaskUserCompleted;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskUserCompleted>
 *
 * @method TaskUserCompleted|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskUserCompleted|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskUserCompleted[]    findAll()
 * @method TaskUserCompleted[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskUserCompletedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskUserCompleted::class);
    }

//    /**
//     * @return TaskUserCompleted[] Returns an array of TaskUserCompleted objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TaskUserCompleted
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
