<?php

namespace App\Repository;

use App\Entity\HistoryMyGame;
use App\Entity\HmgTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HmgTags>
 *
 * @method HmgTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method HmgTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method HmgTags[]    findAll()
 * @method HmgTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HmgTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HmgTags::class);
    }

    /**
     * @return int Returns le nombre de Hmg
     */
    public function countHmgWithTags($tagId): int
    {

        return $this->createQueryBuilder('tag2')
            ->select('count(hmg.id)')
            ->from(HistoryMyGame::class, 'hmg')
            ->join('hmg.hmgTags', 'tag')
            ->where('tag.id = :tagId')
            ->setParameter('tagId', $tagId)
            ->getQuery()
            ->getSingleScalarResult();

    }

//    /**
//     * @return HmgTags[] Returns an array of HmgTags objects
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

//    public function findOneBySomeField($value): ?HmgTags
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
