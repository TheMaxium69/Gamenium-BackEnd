<?php

namespace App\Repository;

use App\Entity\HmgCopyPurchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgCopyPurchase>
 *
 * @method HmgCopyPurchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgCopyPurchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgCopyPurchase[]    findAll()
 * @method HmgCopyPurchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgCopyPurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgCopyPurchase::class);
    }

//    /**
//     * @return HmgCopyPurchase[] Returns an array of HmgCopyPurchase objects
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

//    public function findOneBySomeField($value): ?HmgCopyPurchase
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
