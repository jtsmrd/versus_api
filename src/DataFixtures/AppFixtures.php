<?php

namespace App\DataFixtures;

use App\Entity\Competition;
use App\Entity\Entry;
use App\Entity\User;
use App\Security\TokenGenerator;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @var User[]
     */
    private $users;

    /**
     * @var Entry[]
     */
    private $entries;

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
            'enabled' => true
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
        $this->users = new ArrayCollection();
        $this->entries = new ArrayCollection();
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
       $this->loadCompetitions($manager);
    }

    public function loadEntries(ObjectManager $manager)
    {
        $mediaIds = ['0F2F12ED-45FD-4778-BF33-AA81D8007324', '09548451-4053-4719-9F60-D64381864F4C'];

        for ($i = 0; $i < 100; $i++) {
            $entry = new Entry();
            $entry->setCaption($this->faker->realText(30));
            $entry->setCategoryId(rand(1, 7));
            $entry->setTypeId(rand(1, 2));
            $entry->setCreateDate($this->faker->dateTimeThisYear);

            $entry->setMediaId($mediaIds[rand(0, 1)]);

            $userReference = $this->getRandomUserReference();

            $entry->setUser($userReference);
            $entry->setFeatured($userReference->getFeatured());
            $entry->setRankId($userReference->getRankId());

            $manager->persist($entry);

            $this->entries->add($entry);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $featured = false;

        for ($i = 0; $i < 100; $i++) {

            $username = $this->faker->userName;

            $user = new User();
            $user->setBio($this->faker->realText(100, 2));
            $user->setCreateDate($this->faker->dateTimeThisYear);
            $user->setName($this->faker->name);
            $user->setEmail($this->faker->email);
            $user->setFeatured($featured);
            $user->setRankId(rand(1, 5));
            $user->setUsername($username);
            $user->setRoles([User::ROLE_USER]);
            $user->setProfileImage('287');
            $user->setBackgroundImage('287');

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                "Passw0rd"
            ));

            $user->setEnabled(true);

            $manager->persist($user);

//            $this->users->add($user);

            $featured = !$featured;
        }

        foreach (self::USERS as $userFixture) {

            $user = new User();
            $user->setBio($this->faker->realText(100, 2));
            $user->setCreateDate($this->faker->dateTimeThisYear);
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setFeatured(true);
            $user->setRankId(rand(1, 5));
            $user->setName($userFixture['name']);
            $user->setProfileImage('287');
            $user->setBackgroundImage('287');

            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $userFixture['password']
                )
            );

            $user->setRoles($userFixture['roles']);
            $user->setEnabled(true);

            $manager->persist($user);

            $this->users->add($user);
        }

        $manager->flush();
    }

    public function loadCompetitions(ObjectManager $manager) {

        for ($i = 0; $i < $this->entries->count(); $i++) {

            $leftEntry = $this->getEntryReference();
            $rightEntry = $this->getEntryReference();

            $competition = new Competition();
            $competition->setTypeId($leftEntry->getTypeId());
            $competition->setCategoryId($leftEntry->getCategoryId());
            $competition->setFeatured($leftEntry->getFeatured());
            $competition->setExtended(false);
            $competition->setActive(true);
            $competition->setLeftEntry($leftEntry);
            $competition->setRightEntry($rightEntry);

            $startDate = new \DateTime();
            $expireDate = $startDate->add(new DateInterval('P1D'));

            $competition->setStartDate($startDate);
            $competition->setExpireDate($expireDate);

            $leftEntryUser = $leftEntry->getUser();
            $rightEntryUser = $rightEntry->getUser();

            $competition->addUser($leftEntryUser);
            $competition->addUser($rightEntryUser);

            $manager->persist($competition);
        }

        $manager->flush();
    }

    /**
     * @return User
     */
    private function getRandomUserReference(): User
    {
        return $this->users[rand(0, $this->users->count() - 1)];
    }

    /**
     * @return Entry
     */
    private function getEntryReference(): Entry
    {
        $entry = $this->entries->last();
        $this->entries->removeElement($entry);
        return $entry;
    }
}
