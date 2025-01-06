<?php

namespace App\Repository;

use App\Entity\HmpCopy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmpCopy>
 *
 * @method HmpCopy|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmpCopy|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmpCopy[]    findAll()
 * @method HmpCopy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmpCopyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmpCopy::class);
    }

//    /**
//     * @return HmpCopy[] Returns an array of HmpCopy objects
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

//    public function findOneBySomeField($value): ?HmpCopy
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
