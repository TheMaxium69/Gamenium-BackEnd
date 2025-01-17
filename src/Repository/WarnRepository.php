<?php

namespace App\Repository;

use App\Entity\Warn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Warn>
 *
 * @method Warn|null find($id, $lockMode = null, $lockVersion = null)
 * @method Warn|null findOneBy(array $criteria, array $orderBy = null)
 * @method Warn[]    findAll()
 * @method Warn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Warn::class);
    }
    public function findLatestWarnByUserAndType($user, $object, $type): ?Warn
    {

        if ($type === 'comment_reply') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.user = :user')
                ->andWhere('v.comment_reply = :comment_reply')
                ->setParameter('user', $user)
                ->setParameter('Comment_reply', $object)
                ->orderBy('v.warn_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'comment') {

            $result = $this->createQueryBuilder('v')
                ->andWhere('v.user = :user')
                ->andWhere('v.Comment = :comment')
                ->setParameter('user', $user)
                ->setParameter('comment', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'profil') {

            $result = $this->createQueryBuilder('v')
                ->andWhere('v.user = :user')
                ->andWhere('v.profil = :profil')
                ->setParameter('user', $user)
                ->setParameter('profil', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'actu') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.user = :user')
                ->andWhere('v.actu = :actu')
                ->setParameter('user', $user)
                ->setParameter('actu', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'hmg') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.user = :user')
                ->andWhere('v.hmg = :hmg')
                ->setParameter('user', $user)
                ->setParameter('hmg', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'hmp') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.user = :user')
                ->andWhere('v.hmp = :hmp')
                ->setParameter('user', $user)
                ->setParameter('hmp', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            $result = null;
        }

        return $result;
    }
    
    public function findLatestWarnByIpAndType($ip, $object, $type): ?Warn
    {

        if ($type === 'comment_reply') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.comment_reply = :comment_reply')
                ->setParameter('ip', $ip)
                ->setParameter('comment_reply', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'comment') {

            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.comment = :comment')
                ->setParameter('ip', $ip)
                ->setParameter('comment', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'profil') {

            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.profil = :profil')
                ->setParameter('ip', $ip)
                ->setParameter('profil', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'actu') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.actu = :actu')
                ->setParameter('ip', $ip)
                ->setParameter('actu', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'hmg') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.hmg = :hmg')
                ->setParameter('ip', $ip)
                ->setParameter('hmg', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'hmp') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.hmp = :hmp')
                ->setParameter('ip', $ip)
                ->setParameter('hmp', $object)
                ->orderBy('v.warnAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            $result = null;
        }

        return $result;
    }
//    /**
//     * @return Warn[] Returns an array of Warn objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Warn
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
