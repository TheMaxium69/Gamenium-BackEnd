<?php

namespace App\Repository;

use App\Entity\HmgCopyDlc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgCopyDlc>
 *
 * @method HmgCopyDlc|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgCopyDlc|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgCopyDlc[]    findAll()
 * @method HmgCopyDlc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgCopyDlcRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgCopyDlc::class);
    }

//    /**
//     * @return HmgCopyDlc[] Returns an array of HmgCopyDlc objects
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

//    public function findOneBySomeField($value): ?HmgCopyDlc
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
