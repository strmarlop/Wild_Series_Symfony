<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Service\ProgramDuration;
use App\Entity\Actor;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\Mapping\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

// use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, MailerInterface $mailer, ProgramRepository $programRepository, SluggerInterface $slugger): Response
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

            $program->setOwner($this->getUser()); //asigno el propietario de la creacion de serie

            // Deal with the submitted data            
            $programRepository->save($program, true);

            $email = (new Email())
                    ->from($this->getParameter('mailer_from'))
                    ->to('your_email@example.com')
                    ->subject('Une nouvelle série vient d\'être publiée !')
                    ->html($this->renderView('Program/newProgramEmail.html.twig', ['program' => $program]));
                    
            $mailer->send($email);


            // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
            //Y antes de ser redirigido al index
            $this->addFlash('success', 'The new program has been created! :)'); //message in base.html.twig, para que sea global

            // Redirect to categories list
            return $this->redirectToRoute('program_index');
        }

        // Render the form
        return $this->render('program/new.html.twig', ['form' => $form,]);
    }

    #[Route('/show/{slug_program}', name: 'show')] //SluggerInterface $slugger no hace falta porque ya lo tienes dentro de programa
    // #[Entity('program',
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

    #[Route('/{slug_program}/season/{season}/episode/{slug_episode}', name: 'episode_show', methods: ['GET', 'POST'])]
    #[ParamConverter('program', options: ['mapping' => ['slug_program' => 'slug']])] // guardado en favoritos en navegador chrome
    #[ParamConverter('episode', options: ['mapping' => ['slug_episode' => 'slug']])] // guardado en favoritos en navegador chrome
    public function showEpisode(CommentRepository $commentRepository, Request $request, Program $program, Season $season, Episode $episode): Response
    {
        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode with id : ' . $episode->getId() . ' found in episode\'s table.'
            );
        }

        //return user Object
        $user = $this->getUser();

        $comment = new Comment();
       
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {        
            $comment->setAuthor($user); // relier commentaire à un user
            $comment->setEpisode($episode); // relier commentaire à un episode
            $commentRepository->save($comment, true);
            $this->addFlash('success', 'The comment has been added! :)');

            return $this->redirectToRoute('program_episode_show', ['slug_program' => $program->getSlug(), 'season' => $season->getId(), 'slug_episode' => $episode->getSlug()], Response::HTTP_SEE_OTHER); //Hacer, ir a la serie con el comentario
        }      

        return $this->render('program/episode_show.html.twig', ['program' => $program, 'season' => $season, 'episode' => $episode, 'form' => $form]);
    }

    #[Route('/edit/{slug_program}', name: 'edit', methods: ['GET', 'POST'])]
    #[ParamConverter('program', options: ['mapping' => ['slug_program' => 'slug']])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository, SluggerInterface $slugger): Response
    {
        // Check wether the logged in user is the owner of the program
        if ($this->getUser() !== $program->getOwner()) {
            // If not the owner, throws a 403 Access Denied exception
            throw $this->createAccessDeniedException('Only the owner can edit the program!');
        }
          
        
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slug($program->getTitle()); //en parametre la chaîne de caractère à sluggifier
            $program->setSlug($slug);

            $program->setOwner($this->getUser()); //asigno el propietario de la creacion de serie
            // Deal with the submitted data            
            $programRepository->save($program, true);
    
            // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
            //Y antes de ser redirigido al index
            $this->addFlash('success', 'The new program has been edited! :)'); //message in base.html.twig, para que sea global

            // Redirect to categories list
            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', ['program' => $program, 'form' => $form]);
    }

    
    

}
