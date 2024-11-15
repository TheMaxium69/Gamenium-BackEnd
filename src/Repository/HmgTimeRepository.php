<?php

namespace App\Repository;

use App\Entity\HmgTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgTime>
 *
 * @method HmgTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgTime[]    findAll()
 * @method HmgTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgTime::class);
    }

//    /**
//     * @return HmgTime[] Returns an array of HmgTime objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HmgTime
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
