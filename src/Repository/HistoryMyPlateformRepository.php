<?php

namespace App\Repository;

use App\Entity\HistoryMyPlateform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoryMyPlateform>
 *
 * @method HistoryMyPlateform|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryMyPlateform|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryMyPlateform[]    findAll()
 * @method HistoryMyPlateform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryMyPlateformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryMyPlateform::class);
    }

//    /**
//     * @return HistoryMyPlateform[] Returns an array of HistoryMyPlateform objects
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

//    public function findOneBySomeField($value): ?HistoryMyPlateform
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
