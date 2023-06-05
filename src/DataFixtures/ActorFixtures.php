<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Program; //
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker=Factory::create();

        for ($numActor = 0; $numActor < 10; $numActor++){
            $actor=new Actor;
            $actor->setName($faker->name());
            for ($numProgram = 0; $numProgram < 3; $numProgram++){ 
            // var_dump($faker->numberBetween(1, count(ProgramFixtures::PROGRAMS)));
            // exit();
                $program=$this->getReference('program_' . $faker->numberBetween(0, count(ProgramFixtures::PROGRAMS)-1)); //entre 0 et num programs-1 
                $actor->addProgram($program);
            }
            $manager->persist($actor);
        }  
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont EpisodeFixtures dépend
        return [
            ProgramFixtures::class,
        ];
    }
}
