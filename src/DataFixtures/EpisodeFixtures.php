<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $episode = new Episode();
        $episode->setSeason($this->getReference('season1_Sherlock'));
        $episode->setTitle('Une étude en rose');
        $episode->setNumber(1);
        $episode->setSynopsis('John Watson, un ex-médecin militaire blessé durant la guerre d\'Afghanistan, fait la connaissance de Sherlock Holmes grâce à un ami commun. Ils décident de devenir colocataires, en partageant un appartement londonien situé 221B Baker Street et dont la logeuse est Mrs Hudson.');
        //... create 2 more episodes

        $manager->persist($episode); //pour enregistrer episode
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
