<?php

namespace App\Repository;

use App\Entity\HmgScreenshot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgScreenshot>
 *
 * @method HmgScreenshot|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgScreenshot|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgScreenshot[]    findAll()
 * @method HmgScreenshot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgScreenshotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgScreenshot::class);
    }

//    /**
//     * @return HmgScreenshot[] Returns an array of HmgScreenshot objects
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

//    public function findOneBySomeField($value): ?HmgScreenshot
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
