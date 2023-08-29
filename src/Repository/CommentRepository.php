<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return Comment[] Returns an array of Comment objects
     */
    public function findApproved($id): array
    {
        return $this->createQueryBuilder('c')
                        ->andWhere('c.approved = :val')
                        ->setParameter('val', 0)
                        ->andWhere('c.gb_id = :gb_id')
                        ->setParameter('gb_id', $id)
                        ->orderBy('c.created_at', 'DESC')
                        ->setMaxResults(10)
                        ->getQuery()
                        ->getResult();
    }
}
