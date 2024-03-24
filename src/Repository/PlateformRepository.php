<?php

namespace App\Repository;

use App\Entity\Plateform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plateform>
 *
 * @method Plateform|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plateform|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plateform[]    findAll()
 * @method Plateform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlateformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plateform::class);
    }

//    /**
//     * @return Plateform[] Returns an array of Plateform objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Plateform
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
