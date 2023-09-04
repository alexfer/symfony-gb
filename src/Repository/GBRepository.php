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

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GB::class);
    }

    public function getAllEntries()
    {
        return $this->createQueryBuilder('gb')
                        ->orderBy('gb.id', 'DESC')
                        ->setMaxResults(10)
                        ->getQuery()
                        ->getResult();
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
