<?php

namespace App\Repository;

use App\Entity\UserDefault;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDefault>
 *
 * @method UserDefault|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDefault|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDefault[]    findAll()
 * @method UserDefault[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDefaultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDefault::class);
    }

//    /**
//     * @return UserDefault[] Returns an array of UserDefault objects
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

//    public function findOneBySomeField($value): ?UserDefault
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
