<?php

namespace App\Repository;

use App\Entity\Devise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Devise>
 *
 * @method Devise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devise[]    findAll()
 * @method Devise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devise::class);
    }

//    /**
//     * @return Devise[] Returns an array of Devise objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Devise
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
