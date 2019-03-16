<?php

namespace App\DataFixtures;

use App\Entity\Entry;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    private const USERS = [
        [
            'bio' => 'Hey it is admin.',
            'name' => 'Admin User',
            'email' => 'admin@versus.com',
            'username' => 'admin',
            'password' => 'Passw0rd',
            'roles' => [User::ROLE_SUPERADMIN],
            'enabled' => true
        ],
        [
            'bio' => 'Hey it is JT.',
            'name' => 'JT Smrdel',
            'email' => 'smrd813@aol.com',
            'username' => 'smrd813',
            'password' => 'Passw0rd',
            'roles' => [User::ROLE_USER],
            'enabled' => false
        ],
        [
            'bio' => 'Hey it is Taylor',
            'name' => 'Taylor Smrdel',
            'email' => 'taylor@versus.com',
            'username' => 'taylorSmrdel',
            'password' => 'Passw0rd',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
        [
            'bio' => 'Hey it is Shane.',
            'name' => 'Shane Krznaric',
            'email' => 'shanecool@hotmail.com',
            'username' => 'shanecool',
            'password' => 'Passw0rd',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
        [
            'bio' => 'Hey it is Bryan.',
            'name' => 'Bryan Scagline',
            'email' => 'bscaggs@gmail.com',
            'username' => 'bscaggs',
            'password' => 'Passw0rd',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
        [
            'bio' => 'Hey it is Adam.',
            'name' => 'Adam Marks',
            'email' => 'marks02@gmail.com',
            'username' => 'marks02',
            'password' => 'Passw0rd',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
    ];

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Load data fixtures with the passed ObjectManager
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
       $this->loadUsers($manager);
       $this->loadEntries($manager);
    }

    public function loadEntries(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $entry = new Entry();
            $entry->setCaption($this->faker->realText(30));
            $entry->setCategoryId(rand(1, 7));
            $entry->setTypeId(rand(1, 2));
            $entry->setCreateDate($this->faker->dateTimeThisYear);
            $entry->setFeatured(false);
            $entry->setMediaId($this->faker->uuid);
            $entry->setRankId(rand(1, 5));

            $userReference = $this->getRandomUserReference();

            $entry->setUser($userReference);

            $manager->persist($entry);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setBio($userFixture['bio']);
            $user->setCreateDate($this->faker->dateTimeThisYear);
            $user->setName($userFixture['name']);
            $user->setEmail($userFixture['email']);
            $user->setFeatured(false);
            $user->setRankId(rand(1, 5));
            $user->setUsername($userFixture['username']);
            $user->setRoles($userFixture['roles']);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));

            $user->setEnabled($userFixture['enabled']);

            if (!$userFixture['enabled']) {
                $user->setConfirmationToken(
                    $this->tokenGenerator->getRandomSecureToken()
                );
            }

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return User
     */
    private function getRandomUserReference(): User
    {
        return $this->getReference('user_' . self::USERS[rand(0, 5)]['username']);
    }
}
