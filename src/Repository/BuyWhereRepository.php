<?php

namespace App\Repository;

use App\Entity\BuyWhere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BuyWhere>
 *
 * @method BuyWhere|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuyWhere|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuyWhere[]    findAll()
 * @method BuyWhere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuyWhereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuyWhere::class);
    }

//    /**
//     * @return BuyWhere[] Returns an array of BuyWhere objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BuyWhere
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
