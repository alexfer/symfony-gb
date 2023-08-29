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
    public function countComments($id, int $approved = 1)
    {
        return $this->createQueryBuilder('c')
                        ->select('count(c.id)')
                        ->andWhere('c.approved = :approved')
                        ->setParameter('approved', $approved)
                        ->andWhere('c.gb_id = :gb_id')
                        ->setParameter('gb_id', $id)
                        ->getQuery()
                        ->getSingleScalarResult();
    }
}
