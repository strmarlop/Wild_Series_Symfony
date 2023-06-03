<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Program; //
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ActorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker=Factory::create();

        for ($numActor = 0; $numActor <= 10; $numActor++){
            for ($numProgram = 0; $numProgram <= 3; $numProgram++){
                $actor=new Actor;
                $actor->setName($faker->name());

                $program=$this->getReference('program_' . rand(1,7)); //Assignem un dels 7 pgm. I el pgm amb el getreference de l'entité Program
                $actor->addProgram($program);

                $manager->persist($actor);
            }
        }  
        $manager->flush();
    }
}
