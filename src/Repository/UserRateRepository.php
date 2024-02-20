<?php

namespace App\Repository;

use App\Entity\UserRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRate>
 *
 * @method UserRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRate[]    findAll()
 * @method UserRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRate::class);
    }

//    /**
//     * @return UserRate[] Returns an array of UserRate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserRate
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
