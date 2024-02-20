<?php

namespace App\Repository;

use App\Entity\NoteUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NoteUser>
 *
 * @method NoteUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method NoteUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method NoteUser[]    findAll()
 * @method NoteUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoteUser::class);
    }

//    /**
//     * @return NoteUser[] Returns an array of NoteUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NoteUser
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
