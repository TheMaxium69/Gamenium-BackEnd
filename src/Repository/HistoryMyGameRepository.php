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
        $resultHmgs = [];
        $selectedIds = [];
        $hmgAllCount = $this->getEntityManager()->getRepository(HistoryMyGame::class)->count();

        for ($i = 1; $i <= $searchNumber; $i++) { 

            do {

                $randomId = random_int(1, $hmgAllCount);

            } while (in_array($randomId, $selectedIds));

            $selectedIds[] = $randomId;

            $conn = $this->getEntityManager()->getConnection();
            $sql = '
                        SELECT hmg.*
                        FROM history_my_game AS hmg
                        LEFT JOIN user u ON u.id = hmg.user_id
                        LEFT JOIN game g ON g.id = hmg.game_id
                        LEFT JOIN plateform p ON p.id = hmg.plateform_id
                        WHERE hmg.id = :id
                    ';

            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery(['id' => $randomId]);
    
            $hmg = $result->fetchAssociative(); 
            
            if ($hmg) {
                $resultHmgs[] = $hmg;
            }
        }
        // var_dump($resultHmgs);
        return $resultHmgs;

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
