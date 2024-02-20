<?php

namespace App\Repository;

use App\Entity\MyAccountExterne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MyAccountExterne>
 *
 * @method MyAccountExterne|null find($id, $lockMode = null, $lockVersion = null)
 * @method MyAccountExterne|null findOneBy(array $criteria, array $orderBy = null)
 * @method MyAccountExterne[]    findAll()
 * @method MyAccountExterne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MyAccountExterneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MyAccountExterne::class);
    }

//    /**
//     * @return MyAccountExterne[] Returns an array of MyAccountExterne objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MyAccountExterne
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
