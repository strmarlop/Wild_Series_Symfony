<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASONS = [
        ['program' => 'Sherlock', 'number' => 1, 'year' => 2010, 'description' => 'Ils s\'inspirent de plusieurs nouvelles de Conan Doyle, le premier épisode étant principalement adapté de Une étude en rose. Les deux autres sont tirés de plusieurs aventures chacun.'],
    ];

    public function load(ObjectManager $manager): void
    {
        $season = new Season();
        $season->setProgram($this->getReference('program_Sherlock'));
        $season->setNumber(1);
        $season->setYear(2010);
        $season->setDescription('Ils s\'inspirent de plusieurs nouvelles de Conan Doyle, le premier épisode étant principalement adapté de Une étude en rose. Les deux autres sont tirés de plusieurs aventures chacun');

        $manager->persist($season); //pour enregistrer
        $this->addReference('season1_Sherlock', $season);
        $manager->flush();


        // foreach (self::SEASONS as $seasonList) {

        //     $season = new Season();
        //     $season->setProgram($this->getReference('program_' . $seasonList['program']));
        //     $season->setNumber($seasonList['number']);
        //     $season->setYear($seasonList['year']);
        //     $season->setDescription($seasonList['description']);

        //     $manager->persist($season); //pour enregistrer
        //     $this->addReference('season1_Sherlock', $season);
        // }
        // $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont SeasonFixtures dépend
        return [
            ProgramFixtures::class,
        ];
    }
}
