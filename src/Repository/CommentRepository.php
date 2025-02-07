<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // Recherche par username/displayName_useretium/post_title/content
    public function searchComment(string $searchValue, int $limit = 10): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.user', 'u')
            ->leftJoin('c.post', 'p')
            ->where('c.content LIKE :searchValue')
            ->orWhere('u.username LIKE :searchValue')
            ->orWhere('u.displayname_useritium LIKE :searchValue')
            ->orWhere('p.title LIKE :searchValue')
            ->setParameter('searchValue', '%' . $searchValue . '%')
            ->setMaxResults($limit)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
