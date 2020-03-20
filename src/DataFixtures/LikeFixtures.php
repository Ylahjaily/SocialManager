<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Proposal;
use App\Entity\Like;

class LikeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        /**
         * on cree un user
         * on cree une soumission
         * on cree un reviewer
         * on cree une review approuvee
         * on fait passer la soumission a "publiée"
         * on cree un utilisateur qui va commenter
         * et on lui attache un commentaire
         */
        //creation de l'user qui va soumettre
        $user_user=new User();
        $user_user->setEmail($faker->safeEmail);
        $user_user->setLastName($faker->lastName);
        $user_user->setFirstName($faker->firstNameFemale);
        $user_user->setPassword($faker->domainWord);
        $manager->persist($user_user);

        //creation de la soumission
        $proposal3 = new Proposal();
        $proposal3->setTitle($faker->name);
        $proposal3->setTextContent($faker->text($maxNbChars = 200));
        $proposal3->setUserId($user_user);
        $manager->persist($proposal3);

        //Creation du reviewer
        $user_reviewer=new User();
        $user_reviewer->setEmail($faker->safeEmail);
        $user_reviewer->setLastName($faker->lastName);
        $user_reviewer->setFirstName($faker->firstNameFemale);
        $user_reviewer->setPassword($faker->domainWord);
        $user_reviewer->setRoles(array('ROLE_REVIEWER'));
        $manager->persist($user_reviewer);

        //creation de la review
        $review_to2 = new Review();
        $review_to2->setProposalId($proposal3);
        $review_to2->setUserId($user_reviewer);
        $review_to2->setIsApproved(true);
        $review_to2->setDecisionAt(new \DateTime('now'));
        $manager->persist($review_to2);


        //changement de l'etat du proposal
        $proposal3->setIsPublished(true);
        $proposal3->setDatePublicationAt(new \DateTime('now'));
        $manager->persist($proposal3);

        //creation de l'user qui like
        $user_fifi=new User();
        $user_fifi->setEmail($faker->safeEmail);
        $user_fifi->setLastName($faker->lastName);
        $user_fifi->setFirstName($faker->firstNameFemale);
        $user_fifi->setPassword($faker->domainWord);
        $manager->persist($user_fifi);

        //creation du commentaire
        $like_x= new Like();
        $like_x->setProposalId($proposal3);
        $like_x->setUserId($user_fifi);
        $manager->persist($like_x);

        $manager->flush();
    }
}
