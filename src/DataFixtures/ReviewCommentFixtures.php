<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\ReviewComment;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Proposal;


class ReviewCommentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        
        /**
         * on cree un user 
         * on cree une soumission
         * on cree une review
         * et on lui attache un commentaire 
         */
        //creation de l'user qui va soumettre
        $user_x=new User();
        $user_x->setEmail($faker->safeEmail);
        $user_x->setLastName($faker->lastName);
        $user_x->setFirstName($faker->firstNameFemale);
        $user_x->setPassword($faker->domainWord);
        $manager->persist($user_x);

        //creation de la soumission
        $proposal2 = new Proposal();    
        $proposal2->setTitle($faker->name);
        $proposal2->setTextContent($faker->text($maxNbChars = 200));
        $proposal2->setUserId($user_x);
        $manager->persist($proposal2);

        //Creation du reviewer
        $user_y=new User();
        $user_y->setEmail($faker->safeEmail);
        $user_y->setLastName($faker->lastName);
        $user_y->setFirstName($faker->firstNameFemale);
        $user_y->setPassword($faker->domainWord);
        $user_y->setRoles(array('ROLE_REVIEWER'));
        $manager->persist($user_y);

        //creation de la review
        $review_to = new Review();
        $review_to->setProposalId($proposal2);
        $review_to->setUserId($user_y);
        $review_to->setIsApproved(false);            
        $manager->persist($review_to);

        //creation du commentaire
        for ($i = 0; $i < 10; $i++) {
            $commReview= new ReviewComment();
            $commReview->setComments($faker->text($maxNbChars = 200));
            $commReview->setReviewId($review_to);
            $commReview->setUserId($user_x);
            $manager->persist($commReview);
        }
        $manager->flush();
    }
}
