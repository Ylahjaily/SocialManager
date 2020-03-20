<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\Review;
use App\Entity\User;


class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        //on créé un user
        $user_review=new User();
        $user_review->setEmail($faker->safeEmail);
        $user_review->setLastName($faker->lastName);
        $user_review->setFirstName($faker->firstNameFemale);
        $user_review->setPassword($faker->domainWord);
        $user_review->setRoles(array('ROLE_REVIEWER'));
        $manager->persist($user_review);


        $review = new Review();
        $review->setProposalId($this->getReference("toto"));
        $review->setUserId($user_review);
        $review->setIsApproved(false);
        $manager->persist($review);

        $manager->flush();
    }
}
