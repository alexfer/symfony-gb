<?php

namespace App\DataFixtures;

use App\Entity\{
    User,
    GB,
};
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class AppFixtures extends Fixture
{

    public const USER_REFERENCE = 'users';

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        
    }

//    public function getDependencies()
//    {
//        return [UserFixtures::class];
//    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadEntries($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$name, $password, $email, $roles, $isVerified, $ip]) {
            $user = new User();
            $user->setName($name);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setIsVerified($isVerified);
            $user->setIp($ip);

            $this->addReference($name, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function loadEntries(ObjectManager $manager): void
    {
        foreach ($this->getEntriesData() as [$title, $message, $user, $uuid, $approved]) {
            $gb = new GB();
            $user = $this->getReference('TestUser');
            $gb->setTitle($title);
            $gb->setMessage($message);

            $gb->setUser($user);
            $gb->setUuid($uuid);
            $gb->setApproved($approved);

            $manager->persist($gb);
        }

        $manager->flush();
    }

    private function getEntriesData(): array
    {
        $user = new User();
        $entries = [];

        foreach (range(1, 3) as $i) {
            // $entryData = [$title, $message, $user, $uuid, $approved];
            $uuid = Uuid::v4();
            $entries[] = [
                sprintf('Entry Title %d', $i),
                sprintf('Entry Message %d', $i),
                $user,
                $uuid,
                1,
            ];
        }

        return $entries;
    }

    /**
     * @return array<array{string, string, string, array<string, boolean, string>}>
     */
    private function getUserData(): array
    {
        return [
            // $userData = [$name, $password, $email, $roles, $isVerified, $ip];
            ['AlexanderSh', '7212104', 'alexandershtyher@gmai.com', [User::ROLE_ADMIN], 1, '0.0.0.0'],
            ['Autoportal', '7212104', 'autoportal@email.ua', [User::ROLE_USER], 0, '0.0.0.0'],
            ['TestUser', '7212104', 'test@example.com', [User::ROLE_USER], 0, '0.0.0.0'],
        ];
    }
}
