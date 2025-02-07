<?php

namespace App\Repository;

use App\Entity\HistoryMyPlateform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoryMyPlateform>
 *
 * @method HistoryMyPlateform|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryMyPlateform|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryMyPlateform[]    findAll()
 * @method HistoryMyPlateform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryMyPlateformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryMyPlateform::class);
    }

    public function getRandomHmp(int $searchNumber): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $resultHmps = [];
        $selectedIds = [];

        for ($i = 1; $i <= $searchNumber; $i++) { 

            do {

                $sql = '
                         SELECT hmp.*
                         FROM history_my_plateform AS hmp
                         LEFT JOIN user u ON u.id = hmp.user_id
                         LEFT JOIN plateform p ON p.id = hmp.plateform_id
                         ORDER BY RAND()
                         LIMIT 1;
                     ';
                
                $stmt = $conn->prepare($sql);
                $result = $stmt->executeQuery();
                
                $hmp = $result->fetchAssociative(); 
            
            } while (in_array($hmp['id'], $selectedIds));

            $selectedIds[] = $hmp['id'];

            if ($hmp) {
                $resultHmps[] = $hmp;
            }
        }

        return $resultHmps;

    }
//    /**
//     * @return HistoryMyPlateform[] Returns an array of HistoryMyPlateform objects
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

//    public function findOneBySomeField($value): ?HistoryMyPlateform
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
