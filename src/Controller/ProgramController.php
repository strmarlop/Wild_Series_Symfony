<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Service\ProgramDuration;
use App\Entity\Actor;
use App\Form\ProgramType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\Mapping\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

// use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RequestStack $requestStack, ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        //session
        $session = $requestStack->getSession();
        if (!$session->has('total')) {  //check if key total exists
            $session->set('total', 0); // if total doesn’t exist in session, it is initialized.
        }
        $total = $session->get('total'); // get actual value in session with ‘total' key.
        //pas besoin d'envoyer $total parce que Twig a accès à Session

        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ProgramRepository $programRepository, SluggerInterface $slugger): Response
    {
        // Create a new Program Object - classe de données
        $program = new Program();

        // Create the associated Form  - classe de formulaire
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slug($program->getTitle()); //en parametre la chaîne de caractère à sluggifier
            $program->setSlug($slug);

            // Deal with the submitted data
            $programRepository->save($program, true);

            // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
            //Y antes de ser redirigido al index
            $this->addFlash('success', 'The new program has been created! :)'); //message in base.html.twig, para que sea global

            // Redirect to categories list
            return $this->redirectToRoute('program_index');
        }

        // Render the form
        return $this->render('program/new.html.twig', ['form' => $form,]);
    }


    // #[Route('/show/{id<^[0-9]+$>}', name: 'show')]
    // public function show(Program $program, ProgramDuration $programDuration): Response //param converter

    #[Route('/show/{slug_program}', name: 'show')] //SluggerInterface $slugger no hace falta porque ya lo tienes dentro de programa
    #[ParamConverter('program', options: ['mapping' => ['slug_program' => 'slug']])] // guardado en favoritos en navegador chrome
    public function show(Program $program, ProgramDuration $programDuration): Response //param converter
    {

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program->getId() . ' found in program\'s table.' //aqui era la id
            );
        }

        return $this->render('program/show.html.twig', ['program' => $program, 'programDuration' => $programDuration->calculate($program)]);
    }

    #[Route('/{slug_program}/seasons/{season}', name: 'season_show')] //no repetir program_season_show, el program_ es genérico
    #[ParamConverter('program', options: ['mapping' => ['slug_program' => 'slug']])] // guardado en favoritos en navegador chrome
    public function showSeason(Program $program, Season $season): Response //param converter
    {
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id : ' . $season->getId() . ' found in season\'s table.'
            );
        }

        return $this->render('program/season_show.html.twig', ['program' => $program, 'season' => $season,]);
    }

    #[Route('/{slug_program}/season/{season}/episode/{slug_episode}', name: 'episode_show')]
    #[ParamConverter('program', options: ['mapping' => ['slug_program' => 'slug']])] // guardado en favoritos en navegador chrome
    #[ParamConverter('episode', options: ['mapping' => ['slug_episode' => 'slug']])] // guardado en favoritos en navegador chrome

    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode with id : ' . $episode->getId() . ' found in episode\'s table.'
            );
        }

        return $this->render('program/episode_show.html.twig', ['program' => $program, 'season' => $season, 'episode' => $episode]);
    }
}
