<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


#[Route('/episode')]
class EpisodeController extends AbstractController
{
    #[Route('/', name: 'app_episode_index', methods: ['GET'])]
    public function index(RequestStack $requestStack ,EpisodeRepository $episodeRepository): Response
    {
        // $session = $requestStack->getSession();
        // if (!$session->has('total')) {  //check if key total exists
        //     $session->set('total', 0); // if total doesn’t exist in session, it is initialized.
        // }
        // $total = $session->get('total');


        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_episode_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EpisodeRepository $episodeRepository, SluggerInterface $slugger): Response
    {
        $episode = new Episode();

        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slug($episode->getTitle()); //en parametre la chaîne de caractère à sluggifier
            $episode->setSlug($slug); //rajouter cette methode en entity episode, lancer pour Entity make:entity, migration, migrate, añadiendo champs slug
            // apres crée leslug, en EpisodeFixture hace add con slug y el titulo dentro

            $episodeRepository->save($episode, true);

            $this->addFlash('success', 'The new episode has been created! :)'); //message in base.html.twig, para que sea global

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_episode_show', methods: ['GET'])]
    #[Route('/{slug_episode}', name: 'app_episode_show', methods: ['GET'])]
    #[ParamConverter('episode', options: ['mapping' => ['slug_episode' => 'slug']])] // guardado en favoritos en navegador chrome
    public function show(Episode $episode): Response
    {
        return $this->render('episode/show.html.twig', ['episode' => $episode,]);
    }

    // #[Route('/{id}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    #[Route('/{slug_episode}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    #[ParamConverter('episode', options: ['mapping' => ['slug_episode' => 'slug']])]
    public function edit(Request $request, Episode $episode, EpisodeRepository $episodeRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slug($episode->getTitle()); //en parametre la chaîne de caractère à sluggifier
            $episode->setSlug($slug);
        
        
            $episodeRepository->save($episode, true);

            $this->addFlash('success', 'The episode has been updated! :)');

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('episode/edit.html.twig', ['episode' => $episode,'form' => $form,]);
    }

    // #[Route('/{id}', name: 'app_episode_delete', methods: ['POST'])]
    #[Route('/{slug_episode}', name: 'app_episode_delete', methods: ['POST'])]
    #[ParamConverter('episode', options: ['mapping' => ['slug_episode' => 'slug']])]
    public function delete(Request $request, Episode $episode, EpisodeRepository $episodeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $episodeRepository->remove($episode, true);
        }

        $this->addFlash('danger', 'Oh! One episode has been deleted! :O');

        return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
    }
}
