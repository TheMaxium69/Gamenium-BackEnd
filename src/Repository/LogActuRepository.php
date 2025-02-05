<?php

namespace App\Repository;

use App\Entity\LogActu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogActu>
 */
class LogActuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogActu::class);
    }
    // recherche par type d'action / titre / ou username
    public function searchLogByAction(string $searchValue, int $limit = 10): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.user', 'u')
            ->leftJoin('l.actu', 'a')
            ->where('l.action LIKE :searchValue')
            ->orWhere('l.route LIKE :searchValue')
            ->orWhere('u.username LIKE :searchValue')
            ->orWhere('a.title LIKE :searchValue')
            ->setParameter('searchValue', '%' . $searchValue . '%')
            ->setMaxResults($limit)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
