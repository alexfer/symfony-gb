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
        $i = 0;
        $total = count($this->getUserData());

        foreach ($this->getEntriesData() as [$title, $message, $user, $uuid, $approved]) {
            $gb = new GB();
            $user = $this->getReference($this->getUserData()[mt_rand(0, $total - 1)][0]);
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
        $phrases = $this->getPhrases();
        shuffle($phrases);

        foreach (range(1, 15) as $i) {
            // $entryData = [$title, $message, $user, $uuid, $approved];
            $uuid = Uuid::v4();
            $entries[] = [
                $phrases[mt_rand(0, count($phrases) - 1)],
                $this->getEntryContent(),
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
            ['JojnSmith', 'jojnsmith', 'jojnsmith@example.com', [User::ROLE_USER], 0, '0.0.0.0'],
            ['Boby', 'boby', 'boby@example.com', [User::ROLE_USER], 0, '0.0.0.0'],
            ['UserTest', 'UserTest', 'usertest@example.com', [User::ROLE_USER], 0, '0.0.0.0'],
        ];
    }

    /**
     * 
     * @return array
     */
    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }

    /**
     * 
     * @return string
     */
    private function getEntryContent(): string
    {
        return 'Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eiusmod tempor
            incididunt ut labore et **dolore magna aliqua**: Duis aute irure dolor in
            reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
            deserunt mollit anim id est laborum.';
    }
}
