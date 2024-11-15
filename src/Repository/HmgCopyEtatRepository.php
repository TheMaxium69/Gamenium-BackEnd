<?php

namespace App\Repository;

use App\Entity\HmgCopyEtat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgCopyEtat>
 *
 * @method HmgCopyEtat|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgCopyEtat|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgCopyEtat[]    findAll()
 * @method HmgCopyEtat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgCopyEtatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgCopyEtat::class);
    }

//    /**
//     * @return HmgCopyEtat[] Returns an array of HmgCopyEtat objects
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

//    public function findOneBySomeField($value): ?HmgCopyEtat
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
