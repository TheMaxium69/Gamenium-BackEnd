<?php

namespace App\Repository;

use App\Entity\LogRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogRole>
 *
 * @method LogRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogRole[]    findAll()
 * @method LogRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogRole::class);
    }

//    /**
//     * @return LogRole[] Returns an array of LogRole objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogRole
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
