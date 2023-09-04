<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{

    /**
     * 
     * @var array
     */
    private array $columns = ['id', 'name', 'email', 'created_at'];
    
    /**
     * 
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * 
     * @param string $orderBy
     * @param string $name
     * @return object|null
     */
    public function findAllUsers(string $orderBy, string $name): ?object
    {
        if (!in_array($name, $this->columns)) {
            $name = 'id';
        }
        return $this->createQueryBuilder('u')
                ->orderBy(sprintf('u.%s', $name), $orderBy);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * 
     * @param PasswordAuthenticatedUserInterface $user
     * @param string $newHashedPassword
     * @return void
     * @throws UnsupportedUserException
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * 
     * @param string $usernameOrEmail
     * @return User|null
     */
    public function loadUserByIdentifier(string $usernameOrEmail): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
                                'SELECT u
                FROM App\Entity\User u
                WHERE u.username = :query
                OR u.email = :query'
                        )
                        ->setParameter('query', $usernameOrEmail)
                        ->getOneOrNullResult();
    }
}
