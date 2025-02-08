<?php

namespace App\Repository;

use App\Entity\HistoryMyGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoryMyGame>
 *
 * @method HistoryMyGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryMyGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryMyGame[]    findAll()
 * @method HistoryMyGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryMyGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryMyGame::class);
    }

    public function getRandomHmg(int $searchNumber): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $resultHmgs = [];
        $selectedIds = [];

        for ($i = 1; $i <= $searchNumber; $i++) { 

            do {

                $sql = '
                         SELECT hmg.*
                         FROM history_my_game AS hmg
                         LEFT JOIN user u ON u.id = hmg.user_id
                         LEFT JOIN game g ON g.id = hmg.game_id
                         LEFT JOIN plateform p ON p.id = hmg.plateform_id
                         ORDER BY RAND()
                         LIMIT 1;
                     ';
                
                $stmt = $conn->prepare($sql);
                $result = $stmt->executeQuery();
                
                $hmg = $result->fetchAssociative(); 
            
            } while (in_array($hmg['id'], $selectedIds));

            $selectedIds[] = $hmg['id'];

            if ($hmg) {
                $resultHmgs[] = $hmg;
            }
        }

        return $resultHmgs;

    }

    public function searchHmg(string $searchValue, int $limit = 10): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.user', 'u')
            ->leftJoin('h.game', 'g')
            ->where('h.id LIKE :searchValue')
            ->orWhere('u.username LIKE :searchValue')
            ->orWhere('u.displayname_useritium LIKE :searchValue')
            ->orWhere('g.name LIKE :searchValue')
            ->setParameter('searchValue', '%' . $searchValue . '%')
            ->setMaxResults($limit)
            ->orderBy('h.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return HistoryMyGame[] Returns an array of HistoryMyGame objects
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

//    public function findOneBySomeField($value): ?HistoryMyGame
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
