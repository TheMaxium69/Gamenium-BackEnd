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

    public function searchPostActuByNameWithView(string $searchValue, int $limit = 10): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
                SELECT p.*, COUNT(v.id) AS views_count
                FROM post_actu p
                LEFT JOIN view v ON v.post_actu_id = p.id
                WHERE p.title LIKE "%'. $searchValue .'%"
                GROUP BY p.id
                ORDER BY views_count DESC
                LIMIT '. $limit .';
            ';
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getLatestPostActu(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderedByDate():array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByProviderOrderedByDate($provider): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.Provider = :provider')
            ->setParameter('provider', $provider)
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestByProvider($provider, $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.Provider = :provider')
            ->setParameter('provider', $provider)
            ->orderBy('p.created_at', 'DESC')
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
