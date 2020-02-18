<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\Proposal;


class ProposalFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        // on créé 10 soumissions
        for ($i = 0; $i < 10; $i++) {
            $proposal = new Proposal();
            
            $proposal->setTitle($faker->name);
            $proposal->setTextContent($faker->text($maxNbChars = 200));
            $proposal->setUserId($this->getReference("tata"));

            $manager->persist($proposal);
        }
        $this->addReference("toto", $proposal);

            $manager->flush();

    }
}
