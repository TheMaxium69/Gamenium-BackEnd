<?php

namespace App\Repository;

use App\Entity\GameActuality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameActuality>
 *
 * @method GameActuality|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameActuality|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameActuality[]    findAll()
 * @method GameActuality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameActualityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameActuality::class);
    }

//    /**
//     * @return GameActuality[] Returns an array of GameActuality objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GameActuality
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
