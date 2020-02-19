<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\SocialNetwork;
use App\Entity\User;


class SocialNetworkFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        

        //Creation du communiquant
        $user_comm=new User();
        $user_comm->setEmail($faker->safeEmail);
        $user_comm->setLastName($faker->lastName);
        $user_comm->setFirstName($faker->firstNameFemale);
        $user_comm->setApiKey($faker->swiftBicNumber);
        $user_comm->setPassword($faker->domainWord);
        $user_comm->setRoles(array('ROLE_COMMUNIQUANT'));
        $manager->persist($user_comm);


        //creation du social network
        $social = new SocialNetwork();
        $social->addUserId($user_comm);
        $social->setName("twitter");

        $manager->persist($social);
        
        $manager->flush();
    }
}
