<?php

namespace App\Repository;

use App\Entity\WarnType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WarnType>
 *
 * @method WarnType|null find($id, $lockMode = null, $lockVersion = null)
 * @method WarnType|null findOneBy(array $criteria, array $orderBy = null)
 * @method WarnType[]    findAll()
 * @method WarnType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarnTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarnType::class);
    }

//    /**
//     * @return WarnType[] Returns an array of WarnType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WarnType
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
