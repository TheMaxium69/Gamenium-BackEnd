<?php

namespace App\Repository;

use App\Entity\LogActu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogActu>
 *
 * @method LogActu|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogActu|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogActu[]    findAll()
 * @method LogActu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogActuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogActu::class);
    }

//    /**
//     * @return LogActu[] Returns an array of LogActu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogActu
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
