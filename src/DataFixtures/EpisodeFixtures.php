<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($numProgram = 0; $numProgram <= 6; $numProgram++) {
            for ($numSeason = 1; $numSeason <= 5; $numSeason++) {
                for ($numEpisode = 1; $numEpisode <= 10; $numEpisode++) {
                    $episode = new Episode();
                    $episode->setSeason($this->getReference('season' . $numSeason . '_' . $numProgram));
                    $episode->setTitle($faker->sentence(3));
                    $episode->setNumber($numEpisode);;
                    $episode->setSynopsis($faker->paragraph(1, true));
                    //... create 2 more episodes

                    $manager->persist($episode); //pour enregistrer episode
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont EpisodeFixtures dépend
        return [
            SeasonFixtures::class,
        ];
    }
}
