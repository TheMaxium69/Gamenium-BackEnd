<?php

namespace App\Repository;

use App\Entity\HmgTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgTags>
 *
 * @method HmgTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgTags[]    findAll()
 * @method HmgTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgTags::class);
    }

//    /**
//     * @return HmgTags[] Returns an array of HmgTags objects
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

//    public function findOneBySomeField($value): ?HmgTags
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
