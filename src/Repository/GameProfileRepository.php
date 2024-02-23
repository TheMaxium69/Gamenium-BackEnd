<?php

namespace App\Repository;

use App\Entity\GameProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameProfile>
 *
 * @method GameProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameProfile[]    findAll()
 * @method GameProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameProfile::class);
    }

//    /**
//     * @return GameProfile[] Returns an array of GameProfile objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GameProfile
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
