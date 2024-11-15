<?php

namespace App\Repository;

use App\Entity\HmgCopyLanguage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgCopyLanguage>
 *
 * @method HmgCopyLanguage|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgCopyLanguage|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgCopyLanguage[]    findAll()
 * @method HmgCopyLanguage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgCopyLanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgCopyLanguage::class);
    }

//    /**
//     * @return HmgCopyLanguage[] Returns an array of HmgCopyLanguage objects
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

//    public function findOneBySomeField($value): ?HmgCopyLanguage
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
