<?php

namespace App\Repository;

use App\Entity\BadgeVersUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BadgeVersUser>
 *
 * @method BadgeVersUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method BadgeVersUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method BadgeVersUser[]    findAll()
 * @method BadgeVersUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgeVersUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BadgeVersUser::class);
    }

//    /**
//     * @return BadgeVersUser[] Returns an array of BadgeVersUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BadgeVersUser
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
