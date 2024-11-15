<?php

namespace App\Repository;

use App\Entity\HmgScreenshotCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgScreenshotCategory>
 *
 * @method HmgScreenshotCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgScreenshotCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgScreenshotCategory[]    findAll()
 * @method HmgScreenshotCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgScreenshotCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgScreenshotCategory::class);
    }

//    /**
//     * @return HmgScreenshotCategory[] Returns an array of HmgScreenshotCategory objects
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

//    public function findOneBySomeField($value): ?HmgScreenshotCategory
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
