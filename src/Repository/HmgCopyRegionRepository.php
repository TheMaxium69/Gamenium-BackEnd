<?php

namespace App\Repository;

use App\Entity\HmgCopyRegion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgCopyRegion>
 *
 * @method HmgCopyRegion|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgCopyRegion|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgCopyRegion[]    findAll()
 * @method HmgCopyRegion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgCopyRegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgCopyRegion::class);
    }

//    /**
//     * @return HmgCopyRegion[] Returns an array of HmgCopyRegion objects
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

//    public function findOneBySomeField($value): ?HmgCopyRegion
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
