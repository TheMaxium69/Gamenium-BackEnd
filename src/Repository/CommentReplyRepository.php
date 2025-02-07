<?php

namespace App\Repository;

use App\Entity\CommentReply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentReply>
 *
 * @method CommentReply|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentReply|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentReply[]    findAll()
 * @method CommentReply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentReplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentReply::class);
    }

    // Recherche par userName/displayName_useretium/title_actu
    public function searchCommentReply(string $searchValue, int $limit = 10): array
    {
        return $this->createQueryBuilder('cr')
            ->leftJoin('cr.user', 'u')
            ->leftJoin('cr.comment', 'c')
            ->leftJoin('c.post', 'p')
            ->where('cr.content LIKE :searchValue')
            ->orWhere('u.username LIKE :searchValue')
            ->orWhere('u.displayname_useritium LIKE :searchValue')
            ->orWhere('p.title LIKE :searchValue')
            ->setParameter('searchValue', '%' . $searchValue . '%')
            ->setMaxResults($limit)
            ->orderBy('cr.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return CommentReply[] Returns an array of CommentReply objects
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

//    public function findOneBySomeField($value): ?CommentReply
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
