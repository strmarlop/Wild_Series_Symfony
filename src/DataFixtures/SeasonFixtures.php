<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($numProgram = 0; $numProgram <= 6; $numProgram++) { //numProgram jusqu'à 6 parce dans ProgramFixtures habia hecho array de arrays y la key es de 0 a 6
            for ($numSeason = 1; $numSeason <= 5; $numSeason++) {
                $season = new Season();
                $season->setProgram($this->getReference('program_' . $numProgram)); //relié à ce numéro de program
                $season->setNumber($numSeason); //relié à ce numéro de saison
                $season->setYear($faker->year());
                $season->setDescription($faker->paragraphs(3, true));

                $manager->persist($season); //pour enregistrer
                $this->addReference('season' . $numSeason . '_' . $numProgram, $season);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont SeasonFixtures dépend
        return [
            ProgramFixtures::class,
        ];
    }
}
