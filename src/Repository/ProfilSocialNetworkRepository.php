<?php

namespace App\Repository;

use App\Entity\ProfilSocialNetwork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfilSocialNetwork>
 *
 * @method ProfilSocialNetwork|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfilSocialNetwork|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfilSocialNetwork[]    findAll()
 * @method ProfilSocialNetwork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilSocialNetworkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilSocialNetwork::class);
    }

//    /**
//     * @return ProfilSocialNetwork[] Returns an array of ProfilSocialNetwork objects
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

//    public function findOneBySomeField($value): ?ProfilSocialNetwork
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
