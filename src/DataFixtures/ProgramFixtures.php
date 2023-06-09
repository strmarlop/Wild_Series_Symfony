<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        ['title' => 'Sherlock', 'synopsis' => 'Les aventures de Sherlock Holmes et de son acolyte de toujours, le docteur Watson, sont transposées au XXIème siècle...', 'category' => 'Aventure', 'country' => 'Royaume-Uni', 'year' => '2010'],
        ['title' => 'Game of Thrones', 'synopsis' => 'Dans un pays où l\'été peut durer plusieurs années et l\'hiver toute une vie, des forces sinistres et surnaturelles se pressent aux portes du Royaume des Sept Couronnes. Pendant ce temps, complots et rivalités se jouent sur le continent pour s\'emparer du Trône de Fer, le symbole du pouvoir absolu.', 'category' => 'Fantastique', 'country' => 'États-Unis', 'year' => '2011'],
        ['title' => 'Better Call Saul', 'synopsis' => 'Avocat peinant à joindre les deux bouts, Jimmy McGill se livre à quelques petites escroqueries pour boucler ses fins de mois. Chemin faisant, il va faire des rencontres qui vont se révéler déterminantes dans son parcours : les criminels Nacho Varga et Mike Ehrmantraut.', 'category' => 'Comedie', 'country' => 'États-Unis', 'year' => '2015'],
        ['title' => 'Demon Slayer', 'synopsis' => 'Les citadins locaux ne s\'aventurent jamais dans les bois la nuit à cause de démons mangeurs d\'hommes. Un jour, le jeune Tanjiro découvre que sa famille s\'est fait massacrer et que la seule survivante, sa sœur Nezuko, est devenue un démon. Ainsi, commence la dure tâche de Tanjiro, celle de combattre les démons et de faire redevenir sa sœur humaine.', 'category' => 'Animation', 'country' => 'Japon', 'year' => '2019'],
        ['title' => 'Rick et Morty', 'synopsis' => 'Un brillant inventeur et son petit fils un peu à l\'Ouest partent à l\'aventure...', 'category' => 'Animation', 'country' => 'États-Unis', 'year' => '2013'],
        ['title' => 'Lastman', 'synopsis' => 'Richard Aldana, un jeune boxeur, se retrouve avec la gamine de son meilleur ami sur les bras. Mais la petite Siri est traquée par une secte de fanatiques qui croient à l’existence de la Vallée des Rois, un monde de légendes dont elle serait la clef.', 'category' => 'Animation', 'country' => 'France', 'year' => '2016'],
        ['title' => 'L\'Attaque des Titans', 'synopsis' => 'Dans un monde ravagé par des titans mangeurs d’homme depuis plus d’un siècle, les rares survivants de l’Humanité n’ont d’autre choix pour survivre que de se barricader dans une cité-forteresse. Le jeune Eren, témoin de la mort de sa mère dévorée par un titan, n’a qu’un rêve : entrer dans le corps d’élite chargé de découvrir l’origine des Titans et les annihiler jusqu’au dernier…', 'category' => 'Animation', 'country' => 'Japon', 'year' => '2013'],
    ];

    public SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        foreach (self::PROGRAMS as $key => $programList) {
            $program = new Program();
            $program->setTitle($programList['title']);
            $program->setSynopsis($programList['synopsis']);
            $program->setCategory($this->getReference('category_' . $programList['category']));
            $program->setCountry($programList['country']);
            $program->setYear($programList['year']);
            $program->setSlug($this->slugger->slug($programList['title'])); //je l'ai dans construct

            $manager->persist($program);
            // $this->addReference('program_' . $programList['title'], $program);
            $this->addReference('program_' . $key, $program);
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
