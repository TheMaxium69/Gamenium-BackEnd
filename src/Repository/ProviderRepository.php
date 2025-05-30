<?php

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Provider>
 *
 * @method Provider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Provider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Provider[]    findAll()
 * @method Provider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function searchProviderByName(string $searchValue, int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.displayName LIKE :searchValue')
            ->setParameter('searchValue', '%' . $searchValue . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function searchProviderByNameWithView(string $searchValue, int $limit = 10): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
                SELECT p.*, COUNT(v.id) AS views_count
                FROM provider p
                LEFT JOIN view v ON v.provider_id = p.id
                WHERE p.tag_name LIKE "%'. $searchValue .'%"
                GROUP BY p.id
                ORDER BY views_count DESC
                LIMIT '. $limit .';
            ';
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

//    /**
//     * @return Provider[] Returns an array of Provider objects
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

//    public function findOneBySomeField($value): ?Provider
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
