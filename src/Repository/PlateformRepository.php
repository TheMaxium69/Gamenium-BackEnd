<?php

namespace App\Repository;

use App\Entity\Plateform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plateform>
 *
 * @method Plateform|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plateform|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plateform[]    findAll()
 * @method Plateform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlateformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plateform::class);
    }


    public function findRandom(int $searchNumber): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $resultActu = [];
        $selectedIds = [];

        for ($i = 1; $i <= $searchNumber; $i++) {

            do {
                $sql = 'SELECT * FROM plateform WHERE id != 99999 ORDER BY RAND() LIMIT 1';

                $stmt = $conn->prepare($sql);
                $result = $stmt->executeQuery();

                $actu = $result->fetchAssociative();

            } while (in_array($actu['id'], $selectedIds));

            $selectedIds[] = $actu['id'];

            if ($actu) {
                $resultActu[] = $actu;
            }
        }

        return $resultActu;
    }

//    /**
//     * @return Plateform[] Returns an array of Plateform objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Plateform
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
