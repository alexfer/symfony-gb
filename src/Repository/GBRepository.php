<?php

namespace App\Repository;

use App\Entity\GB;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GB>
 *
 * @method GB|null find($id, $lockMode = null, $lockVersion = null)
 * @method GB|null findOneBy(array $criteria, array $orderBy = null)
 * @method GB[]    findAll()
 * @method GB[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GBRepository extends ServiceEntityRepository
{

    /**
     * 
     * @var array
     */
    private array $columns = ['id', 'name', 'title', 'email', 'created_at', 'updated_at'];

    /**
     * 
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GB::class);
    }

    /**
     * 
     * @param string $orderBy
     * @param string $name
     * @return object|null
     */
    public function findAllEntries(string $orderBy, $name): object
    {
        if (!in_array($name, $this->columns)) {
            $name = 'id';
        }

        $column = $name == 'name' ? sprintf('u.%s', $name) : sprintf('g.%s', $name);

        return $this->createQueryBuilder('g')
                        ->leftJoin('g.user', 'u', 'WITH', 'u.id = g.user_id')
                        ->orderBy($column, strtoupper($orderBy));
    }

    /**
     * 
     * @param int $id
     * @return object
     */
    public function findAllEntriesByUserId(int $id, string $orderBy, $name): object
    {
        if (!in_array($name, $this->columns)) {
            $name = 'id';
        }
        
        $column = $name == 'name' ? sprintf('u.%s', $name) : sprintf('g.%s', $name);
        
        return $this->createQueryBuilder('g')
                        ->where('g.user_id = :id')
                        ->setParameter('id', $id)
                        ->leftJoin('g.user', 'u', 'WITH', 'u.id = g.user_id')
                        ->orderBy($column, strtoupper($orderBy));
    }

//    /**
//     * @return GB[] Returns an array of GB objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?GB
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
