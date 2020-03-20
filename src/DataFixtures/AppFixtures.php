<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        //insert 10 users
        for ($i = 0; $i < 10; $i++) 
        {
            $user = new User();
            $user->setEmail($faker->safeEmail);
            $user->setLastName($faker->lastName);
            $user->setFirstName($faker->firstNameFemale);
            $user->setPassword($faker->domainWord);
            $manager->persist($user);
        }
            $this->addReference("tata", $user);
            $manager->flush();

    }
}
