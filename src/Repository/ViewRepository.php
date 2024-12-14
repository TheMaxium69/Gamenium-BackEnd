<?php

namespace App\Repository;

use App\Entity\View;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<View>
 *
 * @method View|null find($id, $lockMode = null, $lockVersion = null)
 * @method View|null findOneBy(array $criteria, array $orderBy = null)
 * @method View[]    findAll()
 * @method View[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, View::class);
    }


    public function findLatestViewByUserAndType($user, $object, $type): ?View
    {

        if ($type === 'game') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.who = :user')
                ->andWhere('v.Game = :game')
                ->setParameter('user', $user)
                ->setParameter('game', $object)
                ->orderBy('v.view_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'provider') {

            $result = $this->createQueryBuilder('v')
                ->andWhere('v.who = :user')
                ->andWhere('v.Provider = :provider')
                ->setParameter('user', $user)
                ->setParameter('provider', $object)
                ->orderBy('v.view_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'profile') {

            $result = $this->createQueryBuilder('v')
                ->andWhere('v.who = :user')
                ->andWhere('v.profile = :profile')
                ->setParameter('user', $user)
                ->setParameter('profile', $object)
                ->orderBy('v.view_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'actu') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.who = :user')
                ->andWhere('v.PostActu = :postActu')
                ->setParameter('user', $user)
                ->setParameter('postActu', $object)
                ->orderBy('v.view_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            $result = null;
        }

        return $result;
    }
    
    public function findLatestViewByIpAndType($ip, $object, $type): ?View
    {

        if ($type === 'game') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.Game = :game')
                ->setParameter('ip', $ip)
                ->setParameter('game', $object)
                ->orderBy('v.view_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'provider') {

            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.Provider = :provider')
                ->setParameter('ip', $ip)
                ->setParameter('provider', $object)
                ->orderBy('v.view_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'profile') {

            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.profile = :profile')
                ->setParameter('ip', $ip)
                ->setParameter('profile', $object)
                ->orderBy('v.view_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        } elseif ($type === 'actu') {
            $result = $this->createQueryBuilder('v')
                ->andWhere('v.ip = :ip')
                ->andWhere('v.PostActu = :postActu')
                ->setParameter('ip', $ip)
                ->setParameter('postActu', $object)
                ->orderBy('v.view_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            $result = null;
        }

        return $result;
    }
    

//    /**
//     * @return View[] Returns an array of View objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?View
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
