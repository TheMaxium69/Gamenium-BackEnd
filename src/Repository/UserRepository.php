<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function searchUserByName(string $searchValue, int $limit = 10): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username LIKE :searchValue OR u.displayname_useritium LIKE :searchValue')
            ->setParameter('searchValue', '%' . $searchValue . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function searchRoleByUser(string $roleName, int $limit = 10): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :searchValue')
            ->setParameter('searchValue', '%' . $roleName . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function searchProfilByNameWithView(string $searchValue, int $limit = 10): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
                SELECT u.*, COUNT(v.id) AS views_count
                FROM user u
                LEFT JOIN view v ON v.profile_id = u.id
                WHERE u.username LIKE "%'. $searchValue .'%"
                GROUP BY u.id
                ORDER BY views_count DESC
                LIMIT '. $limit .';
            ';
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getRandomUser(int $searchNumber): array
    {
        $resultUsers = [];
        $userAllCount = $this->getEntityManager()->getRepository(User::class)->count();
        $selectedIds = [];

        for ($i = 1; $i <= $searchNumber; $i++) { 

            do {

                $randomId = random_int(1, $userAllCount);

            } while (in_array($randomId, $selectedIds));

            $selectedIds[] = $randomId;

            $conn = $this->getEntityManager()->getConnection();
            $sql = 'SELECT * FROM user WHERE id = :id';

            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery(['id' => $randomId]);
    
            $user = $result->fetchAssociative(); 

            if ($user) {
                $resultUsers[] = $user;
            }
        }

        return $resultUsers;
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
