<?php

namespace App\DataFixtures;

use App\Entity\Competition;
use App\Entity\Entry;
use App\Entity\Leaderboard;
use App\Entity\LeaderboardType;
use App\Entity\NotificationType;
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
     * @var User[]
     */
    private $adminUsers;

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
        $this->adminUsers = new ArrayCollection();
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

       // Load entries for admin users first
       $this->loadImageEntries($manager, true, 100);
       $this->loadVideoEntries($manager, true, 100);

       // Load entries for non-admin users
       $this->loadImageEntries($manager, false, 500);
       $this->loadVideoEntries($manager, false, 500);

       $this->loadLeaderboards($manager);
       $this->loadNotificationTypes($manager);
//       $this->loadCompetitions($manager);

    }

    public function loadImageEntries(
        ObjectManager $manager,
        bool $useAdmin,
        int $numEntries
    ) {
        $mediaIds = [
            '0F2F12ED-45FD-4778-BF33-AA81D8007324',
            '09548451-4053-4719-9F60-D64381864F4C',
            '06905d1b-f9ed-45c4-b869-73a620653149',
            '073188de-d106-4d52-8787-0c800ed9ea5b',
            '081b1c6f-f938-42c0-886a-c15cf5db112a',
            '0a0c59a1-dedf-40b1-9368-345a7571d71d'
        ];

        for ($i = 0; $i < $numEntries; $i++) {
            $entry = new Entry();
            $entry->setCaption($this->faker->realText(30));
            $entry->setCategoryId(rand(1, 7));
            $entry->setTypeId(1);
            $entry->setCreateDate($this->faker->dateTimeThisYear);

            $entry->setMediaId($mediaIds[rand(0, 5)]);

            $userReference = $this->getRandomUserReference($useAdmin);

            $entry->setUser($userReference);
            $entry->setFeatured($userReference->getFeatured());
            $entry->setRankId($userReference->getRankId());
            $entry->setVoteCount(rand(0, 5000));

            $manager->persist($entry);

            $this->entries->add($entry);
        }

        $manager->flush();
    }

    public function loadVideoEntries(
        ObjectManager $manager,
        bool $useAdmin,
        int $numEntries
    ) {
        $mediaIds = [
            '1EAF80D6-4277-48B6-8738-195BD7C5BDE8',
            '548AC657-FB1A-43C5-916E-CC7918B6A11E',
            '62B01AF0-3DFE-4F31-A6BC-87DA73339EF9',
            '651E60AE-C505-4697-9893-207706B0135E',
            '9C3FF7E7-F2EF-4760-8CB8-AF400970F500',
            'A6297DB8-DD95-455D-8890-5122E248F0BA',
            'B4DA862A-1F84-479C-BA36-DBB2583C88DD',
            'DC4DA7CA-80AE-467D-8B60-9A3B36877419',
            'DDDA767D-B7CC-464C-8E73-674E746EF49E'
        ];

        for ($i = 0; $i < $numEntries; $i++) {
            $entry = new Entry();
            $entry->setCaption($this->faker->realText(30));
            $entry->setCategoryId(rand(1, 7));
            $entry->setTypeId(2);
            $entry->setCreateDate($this->faker->dateTimeThisYear);

            $entry->setMediaId($mediaIds[rand(0, 8)]);

            $userReference = $this->getRandomUserReference($useAdmin);

            $entry->setUser($userReference);
            $entry->setFeatured($userReference->getFeatured());
            $entry->setRankId($userReference->getRankId());
            $entry->setVoteCount(rand(0, 5000));

            $manager->persist($entry);

            $this->entries->add($entry);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $featured = false;

        for ($i = 0; $i < 1000; $i++) {

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

            $this->users->add($user);

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

            $this->adminUsers->add($user);
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

            // Set the start date to a random date within the last year
            // for testing purposes

            $startDate = $this->getRandomDate();

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
    private function getRandomUserReference(bool $useAdmin): User
    {
        if ($useAdmin) {
            return $this->users[rand(0, $this->adminUsers->count() - 1)];
        }
        else {
            return $this->users[rand(0, $this->users->count() - 1)];
        }
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

    private function loadLeaderboards(ObjectManager $manager)
    {
        $weeklyLeaderboardType = new LeaderboardType();
        $weeklyLeaderboardType->setName('Weekly');

        $monthlyLeaderboardType = new LeaderboardType();
        $monthlyLeaderboardType->setName('Monthly');

        $allTimeLeaderboardType = new LeaderboardType();
        $allTimeLeaderboardType->setName('All Time');

        $manager->persist($weeklyLeaderboardType);
        $manager->persist($monthlyLeaderboardType);
        $manager->persist($allTimeLeaderboardType);


        $weeklyLeaderboard = new Leaderboard();
        $weeklyLeaderboard->setIsActive(true);
        $weeklyLeaderboard->setResultLimit(50);
        $weeklyLeaderboard->setType($weeklyLeaderboardType);

        $monthlyLeaderboard = new Leaderboard();
        $monthlyLeaderboard->setIsActive(true);
        $monthlyLeaderboard->setResultLimit(50);
        $monthlyLeaderboard->setType($monthlyLeaderboardType);

        $allTimeLeaderboard = new Leaderboard();
        $allTimeLeaderboard->setIsActive(true);
        $allTimeLeaderboard->setResultLimit(50);
        $allTimeLeaderboard->setType($allTimeLeaderboardType);

        $manager->persist($weeklyLeaderboard);
        $manager->persist($monthlyLeaderboard);
        $manager->persist($allTimeLeaderboard);


        $manager->flush();
    }

    private function loadNotificationTypes(ObjectManager $manager)
    {
        $typeNames = [
            'New Follower',
            'Competition Matched',
            'New Vote',
            'Competition Won',
            'Competition Lost',
            'Rank Up',
            'Leaderboard',
            'Top Leader',
            'New Followed User Competition',
            'New Comment',
            'New Direct Message'
        ];

        foreach ($typeNames as $name) {
            $this->addNotificationType($manager, $name);
        }

        $manager->flush();
    }

    private function addNotificationType(ObjectManager $manager, string $name)
    {
        $notificationType = new NotificationType();
        $notificationType->setName($name);

        $manager->persist($notificationType);
    }

    private function getRandomDate(): \DateTime
    {
        $todayDate = new \DateTime();

        // ONE MONTH AGO
        $dateInterval = new DateInterval('P1M');
        $dateInterval->invert = 1;
        $oneYearAgo = new \DateTime();
        $oneYearAgo = $oneYearAgo->add($dateInterval);

        $oneYearAgoUnix = $oneYearAgo->getTimestamp();
        $todayDateUnix = $todayDate->getTimestamp();

        $randomDateUnix = rand($oneYearAgoUnix, $todayDateUnix);

        return new \DateTime('@' . $randomDateUnix);
    }
}
