<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        ['title' => 'The Last Of Us', 'synopsis' => 'L\'adaptation du jeu vidéo The Last Of Us en série.', 'category' => 'Aventure'],
        ['title' => 'Game of Thrones', 'synopsis' => 'Dans un pays où l\'été peut durer plusieurs années et l\'hiver toute une vie, des forces sinistres et surnaturelles se pressent aux portes du Royaume des Sept Couronnes. Pendant ce temps, complots et rivalités se jouent sur le continent pour s\'emparer du Trône de Fer, le symbole du pouvoir absolu.', 'category' => 'Fantastique'],
        ['title' => 'Notre planète', 'synopsis' => 'La série présente les espèces les plus précieuses et les habitats les plus fragiles de la planète.', 'category' => 'Documentaire'],
        ['title' => 'American Horror Story', 'synopsis' => 'A chaque saison, son histoire. American Horror Story nous embarque dans des récits à la fois poignants et cauchemardesques, mêlant la peur, le gore et le politiquement correct. De quoi vous confronter à vos plus grandes frayeurs !', 'category' => 'Horreur'],
        ['title' => 'Arcane', 'synopsis' => 'Série animée qui se déroule dans l\'univers de la franchise de jeu vidéo "League of Legends". Intitulée "Arcane", celle-ci raconte les histoires originelles de deux champions emblématiques de la League et le pouvoir qui finit par les déchirer.', 'category' => 'Animation'],
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::PROGRAMS as $programList) {
            // foreach (self::PROGRAMS as $key => $programList) {
            $program = new Program();
            $program->setTitle($programList['title']);
            $program->setSynopsis($programList['synopsis']);
            $program->setCategory($this->getReference('category_' . $programList['category']));

            $manager->persist($program);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}
