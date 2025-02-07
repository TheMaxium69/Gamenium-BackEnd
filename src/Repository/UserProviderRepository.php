<?php

namespace App\Repository;

use App\Entity\UserProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserProvider>
 *
 * @method UserProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProvider[]    findAll()
 * @method UserProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProvider::class);
    }

//    /**
//     * @return UserProvider[] Returns an array of UserProvider objects
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

//    public function findOneBySomeField($value): ?UserProvider
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
