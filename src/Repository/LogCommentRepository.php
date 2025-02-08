<?php

namespace App\Repository;

use App\Entity\LogComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogComment>
 *
 * @method LogComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogComment[]    findAll()
 * @method LogComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogComment::class);
    }

    public function searchLogComment(string $searchValue, int $limit = 10): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.user', 'u')
            ->where('l.content LIKE :searchValue')
            ->orWhere('l.id LIKE :searchValue')
            ->orWhere('u.username LIKE :searchValue')
            ->orWhere('u.displayname_useritium LIKE :searchValue')
            ->setParameter('searchValue', '%' . $searchValue . '%')
            ->setMaxResults($limit)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return LogComment[] Returns an array of LogComment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogComment
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
