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
        ['title' => 'Rick et Morty', 'synopsis' => 'Un brillant inventeur et son petit fils un peu à l\'Ouest partent à l\'aventure...', 'category' => 'Animation'],
        ['title' => 'Lastman', 'synopsis' => 'Richard Aldana, un jeune boxeur, se retrouve avec la gamine de son meilleur ami sur les bras. Mais la petite Siri est traquée par une secte de fanatiques qui croient à l’existence de la Vallée des Rois, un monde de légendes dont elle serait la clef.', 'category' => 'Animation'],
        ['title' => 'L\'Attaque des Titans', 'synopsis' => 'Dans un monde ravagé par des titans mangeurs d’homme depuis plus d’un siècle, les rares survivants de l’Humanité n’ont d’autre choix pour survivre que de se barricader dans une cité-forteresse. Le jeune Eren, témoin de la mort de sa mère dévorée par un titan, n’a qu’un rêve : entrer dans le corps d’élite chargé de découvrir l’origine des Titans et les annihiler jusqu’au dernier…', 'category' => 'Animation'],

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
