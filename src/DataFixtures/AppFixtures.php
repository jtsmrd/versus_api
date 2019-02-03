<?php

namespace App\DataFixtures;

use App\Entity\Entry;
use App\Entity\User;
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

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
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
            $entry->setUser($this->getReference("user_$i"));

            $manager->persist($entry);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->setBio($this->faker->realText(100));
            $user->setConfirmationToken($this->faker->uuid);
            $user->setCreateDate($this->faker->dateTimeThisYear);
            $user->setName($this->faker->name);
            $user->setEmail($this->faker->email);
            $user->setEnabled(true);
            $user->setFeatured(false);
            $user->setRankId(rand(1, 5));
            $user->setUpdateDate($this->faker->dateTimeThisYear);
            $user->setUsername($this->faker->userName);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'Passw0rd'
            ));

            $this->setReference("user_$i", $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
