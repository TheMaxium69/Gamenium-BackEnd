<?php

namespace App\Repository;

use App\Entity\HistoryMyGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoryMyGame>
 *
 * @method HistoryMyGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryMyGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryMyGame[]    findAll()
 * @method HistoryMyGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryMyGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryMyGame::class);
    }

//    /**
//     * @return HistoryMyGame[] Returns an array of HistoryMyGame objects
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

//    public function findOneBySomeField($value): ?HistoryMyGame
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
