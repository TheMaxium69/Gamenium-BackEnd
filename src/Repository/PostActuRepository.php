<?php

namespace App\Repository;

use App\Entity\PostActu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostActu>
 *
 * @method PostActu|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostActu|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostActu[]    findAll()
 * @method PostActu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostActuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostActu::class);
    }

    public function searchPostActuByName(string $searchValue, int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.title LIKE :searchValue')
            ->setParameter('searchValue', '%' . $searchValue . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getLatestPostActu(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return PostActu[] Returns an array of PostActu objects
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

//    public function findOneBySomeField($value): ?PostActu
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
